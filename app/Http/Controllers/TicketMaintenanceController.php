<?php

namespace App\Http\Controllers;

use App\Models\TicketMaintenance;
use App\Models\AssetMateriel;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Traits\FilterSuperAdmin;

class TicketMaintenanceController extends Controller
{
    use FilterSuperAdmin;
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (auth()->user()?->hasRole('Client')) abort(403, 'Accès interdit aux clients.');
            return $next($request);
        });
        $this->middleware('permission:ticket-view', ['only' => ['index', 'show']]);
        $this->middleware('permission:ticket-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:ticket-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:ticket-delete', ['only' => ['destroy']]);
    }

    /**
     * 1. INDEX
     */
    public function index(Request $request)
    {
        $query = TicketMaintenance::with(['assetMateriel', 'user']);
        
        if (!Auth::user()->hasRole('Admin')) {
            $query->where('user_id', Auth::id());
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('description_panne', 'like', "%{$s}%")
                  ->orWhere('statut_ticket', 'like', "%{$s}%")
                  ->orWhereHas('assetMateriel', fn ($a) => $a->where('num_serie', 'like', "%{$s}%"));
            });
        }
        if ($request->filled('statut_ticket')) {
            $query->where('statut_ticket', $request->statut_ticket);
        }
        if ($request->filled('asset_id')) {
            $query->where('asset_materiel_id', $request->asset_id);
        }

        $tickets = $query->orderByDesc('created_at')->get();
        $assets = AssetMateriel::with('typeMateriel')->get();
        return view('tickets.index', compact('tickets', 'assets'));
    }

    /**
     * 2. CREATE
     */
    public function create()
    {
        $assets = AssetMateriel::all();
        $users = Auth::user()->hasRole('Admin') ? $this->excludeSuperAdminsFromUsers(User::query())->get() : collect([Auth::user()]);
        return view('tickets.create', compact('assets', 'users'));
    }

    /**
     * 3. STORE
     */
    public function store(Request $request)
    {
        $request->validate([
            'asset_materiel_id' => 'required|exists:asset_materiels,id',
            'description_panne' => 'required|string',
        ]);

        // Force l'utilisateur connecté si pas Admin, Admin choisit le user_id dans le formulaire
        $userId = Auth::user()->hasRole('Admin') ? $request->user_id : Auth::id();

        if (!$userId) {
            return back()->withErrors(['user_id' => 'Utilisateur requis.'])->withInput();
        }

        $this->validateNotSuperAdminTarget($request->merge(['user_id' => $userId]));

        $this->validateNotSuperAdminTarget($request);

        DB::transaction(function () use ($request, $userId) {
            TicketMaintenance::create([
                'asset_materiel_id' => $request->asset_materiel_id,
                'user_id'           => $userId,
                'description_panne' => $request->description_panne,
                'cout_reparation'   => $request->cout_reparation ?? 0,
                'statut_ticket'     => $request->statut_ticket ?? 'signale',
            ]);

            if (in_array($request->statut_ticket ?? 'signale', ['signale', 'en_atelier'])) {
                AssetMateriel::where('id', $request->asset_materiel_id)->update(['statut_materiel' => 'en_panne']);
            }
        });

        return redirect()->route('tickets.index')->with('success', 'Ticket créé avec succès.');
    }

    /**
     * 4. SHOW
     */
    public function show($id)
    {
        $ticket = TicketMaintenance::with(['assetMateriel', 'user'])->findOrFail($id);

        if (!Auth::user()->hasRole('Admin') && $ticket->user_id != Auth::id()) {
            abort(403);
        }

        return view('tickets.show', compact('ticket'));
    }

    /**
     * 5. EDIT
     */
    public function edit($id)
    {
        $this->denyInventaireMutation();

        $ticket = TicketMaintenance::findOrFail($id);

        if (!Auth::user()->hasRole('Admin') && $ticket->user_id != Auth::id()) {
            abort(403);
        }

        $assets = AssetMateriel::all();
        $users = Auth::user()->hasRole('Admin') ? $this->excludeSuperAdminsFromUsers(User::query())->get() : collect([Auth::user()]);
        return view('tickets.edit', compact('ticket', 'assets', 'users'));
    }

    /**
     * 6. UPDATE
     */
    public function update(Request $request, $id)
    {
        $this->denyInventaireMutation();

        $ticket = TicketMaintenance::findOrFail($id);

        if (!Auth::user()->hasRole('Admin') && $ticket->user_id != Auth::id()) {
            abort(403);
        }

        $request->validate([
            'asset_materiel_id' => 'required|exists:asset_materiels,id',
            'user_id'           => 'required|exists:users,id',
            'description_panne' => 'required|string',
            'cout_reparation'   => 'required|numeric|min:0',
            'statut_ticket'     => 'required|in:signale,en_atelier,resolu',
        ]);

        if (!Auth::user()->hasRole('Admin') && $request->user_id != Auth::id()) {
            abort(403);
        }

        $this->validateNotSuperAdminTarget($request);

        DB::transaction(function () use ($request, $ticket) {
            $ticket->update([
                'asset_materiel_id' => $request->asset_materiel_id,
                'user_id'           => $request->user_id,
                'description_panne' => $request->description_panne,
                'cout_reparation'   => $request->cout_reparation,
                'statut_ticket'     => $request->statut_ticket,
            ]);

            // Mettre à jour le statut du matériel en conséquence
            if ($request->statut_ticket === 'resolu') {
                AssetMateriel::where('id', $request->asset_materiel_id)->update(['statut_materiel' => 'disponible']);
            }
        });

        return redirect()->route('assets.show', $ticket->asset_materiel_id)->with('success', 'Ticket mis à jour avec succès.') ->setTargetUrl(route('assets.show', $ticket->asset_materiel_id) . '#maintenance');
    }

    /**
     * 7. DESTROY
     */
    public function destroy($id)
    {
        $this->denyInventaireMutation();

        $ticket = TicketMaintenance::findOrFail($id);

        if (!Auth::user()->hasRole('Admin')) {
            abort(403);
        }

        DB::transaction(function () use ($ticket) {
            $ticket->delete();
        });

        return redirect()->route('tickets.index')->with('success', 'Ticket supprimé avec succès.');
    }

    /**
     * 8. UPDATE STATUT (Asynchrone Alpine Fetch pour le Helpdesk)
     */
    public function updateStatut(Request $request, TicketMaintenance $ticket)
    {
        $this->denyInventaireMutation();

        $request->validate([
            'statut_ticket' => 'required|in:signale,en_atelier,resolu',
        ]);

        if (!Auth::user()->hasRole('Admin') && !Auth::user()->hasPermissionTo('manage-assets') && !Auth::user()->hasPermissionTo('ticket-edit')) {
            return response()->json(['error' => 'Non autorisé'], 403);
        }

        DB::transaction(function () use ($request, $ticket) {
            $ticket->update([
                'statut_ticket' => $request->statut_ticket,
            ]);

            // Verrou de Panne : Mettre à jour le statut du matériel en conséquence
            if ($request->statut_ticket === 'en_atelier') {
                AssetMateriel::where('id', $ticket->asset_materiel_id)->update(['statut_materiel' => 'en_panne']);
            } elseif ($request->statut_ticket === 'resolu') {
                $asset = AssetMateriel::find($ticket->asset_materiel_id);
                // S'il est assigné à quelqu'un en ce moment, on le remet 'attribue', sinon 'disponible'
                $estAssigne = DB::table('assignation_materiels')
                    ->where('asset_materiel_id', $asset->id)
                    ->whereNull('date_restitution')
                    ->exists();

                $asset->update(['statut_materiel' => $estAssigne ? 'attribue' : 'disponible']);
            }
        });

        return response()->json([
            'success'       => true,
            'statut_ticket' => $request->statut_ticket,
            'message'       => 'Statut du ticket mis à jour.',
        ]);
    }
}
