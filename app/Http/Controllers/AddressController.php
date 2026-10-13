<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Address;

class AddressController extends Controller
{
    public function index()
    {
        $addresses = auth()->user()->addresses ?? collect();
        // fallback if no relation defined:
        $addresses = Address::where('user_id', auth()->id())->get();

        return view('addresses.index', compact('addresses'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'label'       => ['required', 'string', 'max:50'],
            'full_name'   => ['required', 'string', 'max:120'],
            'phone'       => ['required', 'string', 'max:20'],
            'line1'       => ['required', 'string', 'max:255'],
            'line2'       => ['nullable', 'string', 'max:255'],
            'city'        => ['required', 'string', 'max:100'],
            'state'       => ['nullable', 'string', 'max:100'],
            'postal_code' => ['required', 'string', 'max:20'],
            'country'     => ['required', 'string', 'size:2'],
        ]);

        $data['user_id'] = auth()->id();
        $data['is_serviceable'] = $this->isServiceable($data['postal_code']);

        if (!empty($data['is_serviceable'])) {
            Address::where('user_id', auth()->id())->update(['is_default' => false]);
            $data['is_default'] = true;
        }

        $address = Address::create($data);

        if ($request->expectsJson()) {
            return response()->json([
                'ok' => true,
                'address' => $address,
                'is_serviceable' => $address->is_serviceable,
            ]);
        }

        return back()->with('success', 'Address added');
    }

    /** POST /addresses/check-serviceability { postal_code } */
    public function checkServiceability(Request $request)
    {
        $data = $request->validate(['postal_code' => ['required', 'digits:6']]);
        $ok = $this->isServiceable($data['postal_code']);

        return response()->json([
            'ok' => true,
            'serviceable' => $ok,
            'message' => $ok
                ? 'Great! We deliver to this pincode.'
                : 'Sorry, we do not yet deliver to this pincode.',
        ]);
    }

    public function destroy(Address $address)
    {
        abort_unless($address->user_id === auth()->id(), 403);
        $address->delete();

        return back()->with('success', 'Address removed');
    }

    /** Replace with real serviceability rules — zones, pincode lists, etc. */
    private function isServiceable(string $postalCode): bool
    {
        // Demo: pincodes starting with "1", "4", "5", "6", "7" are serviceable
        return str_starts_with(trim($postalCode), '1')
            || str_starts_with(trim($postalCode), '4')
            || str_starts_with(trim($postalCode), '5')
            || str_starts_with(trim($postalCode), '6')
            || str_starts_with(trim($postalCode), '7');
    }
}
