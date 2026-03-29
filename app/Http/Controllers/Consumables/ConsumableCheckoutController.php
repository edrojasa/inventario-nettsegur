<?php

namespace App\Http\Controllers\Consumables;

use App\Events\CheckoutableCheckedOut;
use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Models\Consumable;
use App\Models\ConsumableTransaction;
use App\Models\Location;
use App\Models\User;
use Illuminate\Http\Request;
use \Illuminate\Contracts\View\View;
use \Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;

class ConsumableCheckoutController extends Controller
{
    /**
     * Return a view to checkout a consumable to a user.
     *
     * @author [A. Gianotto] [<snipe@snipe.net>]
     * @see ConsumableCheckoutController::store() method that stores the data.
     * @since [v1.0]
     * @param int $id
     */
    public function create($id) : View | RedirectResponse
    {

        if ($consumable = Consumable::find($id)) {

            $this->authorize('checkout', $consumable);

            // Make sure the category is valid
            if ($consumable->category) {

                // Make sure there is at least one available to checkout
                if ($consumable->numRemaining() <= 0){
                    return redirect()->route('consumables.index')
                        ->with('error', trans('admin/consumables/message.checkout.unavailable', ['requested' => 1, 'remaining' => $consumable->numRemaining()]));
                }

                // Return the checkout view
                return view('consumables/checkout', compact('consumable'));
            }

            // Invalid category
            return redirect()->route('consumables.edit', ['consumable' => $consumable->id])
                ->with('error', trans('general.invalid_item_category_single', ['type' => trans('general.consumable')]));
        }

        // Not found
        return redirect()->route('consumables.index')->with('error', trans('admin/consumables/message.does_not_exist'));

    }

    /**
     * Saves the checkout information
     *
     * @author [A. Gianotto] [<snipe@snipe.net>]
     * @see ConsumableCheckoutController::create() method that returns the form.
     * @since [v1.0]
     * @param int $consumableId
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function store(Request $request, $consumableId)
    {
        if (is_null($consumable = Consumable::with('users')->find($consumableId))) {
            return redirect()->route('consumables.index')->with('error', trans('admin/consumables/message.not_found'));
        }

        $this->authorize('checkout', $consumable);

        $quantity = $request->input('checkout_qty');
        if (! isset($quantity) || ! ctype_digit((string) $quantity) || (int) $quantity <= 0) {
            $quantity = 1;
        }
        $quantity = (int) $quantity;

        if ($consumable->numRemaining() <= 0 || $quantity > $consumable->numRemaining()) {
            return redirect()->route('consumables.index')->with('error', trans('admin/consumables/message.checkout.unavailable', ['requested' => $quantity, 'remaining' => $consumable->numRemaining()]));
        }

        $adminUser = auth()->user();
        if (! $adminUser) {
            return redirect()->guest(route('login'));
        }

        $targetType = $request->input('checkout_to_type', 'user');
        if (! in_array($targetType, ['user', 'location'], true)) {
            return redirect()->route('consumables.checkout.show', $consumable)
                ->with('error', trans('validation.in', ['attribute' => 'checkout_to_type']))
                ->withInput();
        }

        $user = null;
        $userId = null;
        $locationId = null;

        if ($targetType === 'user') {
            $userId = (int) $request->input('assigned_to');
            if ($userId < 1 || is_null($user = User::find($userId))) {
                return redirect()->route('consumables.checkout.show', $consumable)
                    ->with('error', trans('admin/consumables/message.checkout.user_does_not_exist'))
                    ->withInput();
            }
        } else {
            $locationId = (int) $request->input('assigned_location');
            if ($locationId < 1 || ! Location::whereKey($locationId)->exists()) {
                return redirect()->route('consumables.checkout.show', $consumable)
                    ->with('error', trans('admin/locations/message.does_not_exist'))
                    ->withInput();
            }
        }

        $note = $request->input('note');
        $checkoutTransactionId = null;

        DB::transaction(function () use ($consumable, $quantity, $targetType, $userId, $locationId, $adminUser, $note, &$checkoutTransactionId) {
            if ($targetType === 'user') {
                $consumable->assigned_to = $userId;
                $records = [];
                for ($i = 0; $i < $quantity; $i++) {
                    $records[] = [
                        'consumable_id' => $consumable->id,
                        'created_by' => $adminUser->id,
                        'assigned_to' => $userId,
                        'note' => $note,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
                DB::table('consumables_users')->insert($records);
            }

            $transaction = ConsumableTransaction::create([
                'consumable_id' => $consumable->id,
                'type' => $targetType,
                'user_id' => $targetType === 'user' ? $userId : null,
                'location_id' => $targetType === 'location' ? $locationId : null,
                'remision_id' => null,
                'quantity' => $quantity,
                'status' => 'entregado',
                'assigned_by' => $adminUser->id,
                'notes' => $note,
            ]);

            $checkoutTransactionId = $transaction->id;
        });

        $consumable->checkout_qty = $quantity;

        if ($targetType === 'user' && $user) {
            event(new CheckoutableCheckedOut(
                $consumable,
                $user,
                $adminUser,
                $note,
                [],
                $consumable->checkout_qty,
            ));

            $request->request->add(['checkout_to_type' => 'user']);
            $request->request->add(['assigned_user' => $user->id]);
        }

        if ($request->filled('generate_remision') && $checkoutTransactionId !== null) {
            return redirect()->route('remision.show', ['consumables' => [$checkoutTransactionId]]);
        }

        session()->put(['redirect_option' => $request->input('redirect_option'), 'checkout_to_type' => $request->input('checkout_to_type')]);

        return Helper::getRedirectOption($request, $consumable->id, 'Consumables')
            ->with('success', trans('admin/consumables/message.checkout.success'));
    }
}
