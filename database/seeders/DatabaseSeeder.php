<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\User;
use App\Services\StockService;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Local development / demo data only.
 * Idempotent (firstOrCreate) and refuses to run in production.
 */
class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        if (app()->environment('production')) {
            $this->command?->error('Demo seeder refused: APP_ENV=production.');

            return;
        }

        $stock = app(StockService::class);

        DB::transaction(function () use ($stock) {
            $created = [];
            $admin = $this->user('StockWise Admin', 'admin@stockwise.test', User::ROLE_ADMIN);
            $staffA = $this->user('Nimal Perera', 'staff1@stockwise.test', User::ROLE_STAFF);
            $staffB = $this->user('Kavindi Silva', 'staff2@stockwise.test', User::ROLE_STAFF);

            $categories = collect([
                'Electronics' => 'Devices, components and accessories',
                'Office Supplies' => 'Stationery and office consumables',
                'Furniture' => 'Desks, chairs and storage',
                'Networking' => 'Routers, switches and cabling',
            ])->map(fn ($desc, $name) => Category::firstOrCreate(['name' => $name], ['description' => $desc]));

            $suppliers = collect([
                ['name' => 'Lanka Tech Distributors', 'email' => 'sales@lankatech.example', 'phone' => '+94 11 234 5678', 'address' => '12 Galle Road, Colombo 03'],
                ['name' => 'Office World Pvt Ltd', 'email' => 'orders@officeworld.example', 'phone' => '+94 11 876 5432', 'address' => '45 Kandy Road, Kelaniya'],
                ['name' => 'Ceylon Furniture Co.', 'email' => null, 'phone' => '+94 81 222 3344', 'address' => 'Peradeniya Road, Kandy'],
            ])->mapWithKeys(fn ($s) => [$s['name'] => Supplier::firstOrCreate(['name' => $s['name']], $s)]);

            $products = [
                // [owner, sku, name, category, supplier, purchase, selling, qty, min]
                [$staffA, 'ELEC-001', 'Wireless Mouse', 'Electronics', 'Lanka Tech Distributors', 1500, 2200, 40, 10],
                [$staffA, 'ELEC-002', 'USB-C Charger 65W', 'Electronics', 'Lanka Tech Distributors', 4200, 5900, 6, 8],
                [$staffA, 'OFF-001', 'A4 Paper Ream', 'Office Supplies', 'Office World Pvt Ltd', 1100, 1450, 0, 20],
                [$staffB, 'FUR-001', 'Ergonomic Office Chair', 'Furniture', 'Ceylon Furniture Co.', 28000, 36500, 12, 3],
                [$staffB, 'NET-001', 'Gigabit Switch 8-Port', 'Networking', 'Lanka Tech Distributors', 9500, 12900, 3, 5],
                [$staffB, 'OFF-002', 'Ballpoint Pens (Box of 50)', 'Office Supplies', null, 900, 1300, 75, 15],
                [$admin, 'NET-002', 'Cat6 Cable 305m', 'Networking', null, 21000, 27500, 9, 4],
            ];

            foreach ($products as [$owner, $sku, $name, $cat, $sup, $buy, $sell, $qty, $min]) {
                if (Product::withTrashed()->where('sku', $sku)->exists()) {
                    continue;
                }

                $product = new Product([
                    'name' => $name,
                    'sku' => $sku,
                    'category_id' => $categories[$cat]->id,
                    'supplier_id' => $sup ? $suppliers[$sup]->id : null,
                    'purchase_price' => $buy,
                    'selling_price' => $sell,
                    'min_stock_level' => $min,
                ]);
                $product->created_by = $owner->id;
                $product->quantity = 0;
                $product->save();

                // Same path as the UI: initial stock is an audited IN movement via StockService.
                if ($qty > 0) {
                    $stock->stockIn($product, $owner, $qty, 'Initial stock');
                }
                $created[$sku] = [$product, $owner];
            }

            // A few sample movements so history, dashboards and reports have data.
            if (isset($created['ELEC-001'])) {
                [$p, $u] = $created['ELEC-001'];
                $stock->stockIn($p, $u, 10, 'Supplier delivery');
                $stock->stockOut($p, $u, 15, 'Sales order');
            }
            if (isset($created['FUR-001'])) {
                [$p, $u] = $created['FUR-001'];
                $stock->adjust($p, $u, 11, 'Stock count: one chair damaged');
            }
            if (isset($created['NET-002'])) {
                [$p] = $created['NET-002'];
                $stock->stockOut($p, $admin, 2, 'Internal installation');
            }
        });
    }

    private function user(string $name, string $email, string $role): User
    {
        $user = User::firstOrNew(['email' => $email]);

        if (! $user->exists) {
            $user->name = $name;
            $user->password = 'password'; // hashed by the model cast; local demo only
            $user->role = $role;
            $user->status = User::STATUS_ACTIVE;
            $user->email_verified_at = now();
            $user->save();
        }

        return $user;
    }
}
