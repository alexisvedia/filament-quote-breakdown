<?php

namespace Database\Seeders;

use App\Models\Supplier;
use Illuminate\Database\Seeder;

class SupplierSeeder extends Seeder
{
    public function run(): void
    {
        $suppliers = [
            [
                'company' => 'Textile Manufacturers Ltd',
                'legal_name' => 'Textile Manufacturers Limited',
                'contact_name' => 'Robert Chen',
                'email' => 'robert@textilemanufacturers.com',
                'phone' => '+86 21 5555 8888',
                'country' => 'China',
                'city' => 'Shanghai',
                'currency' => 'USD',
                'payment_terms' => 'Net 60',
                'supplier_category' => 'Fabric',
                'capabilities' => 'Jersey, Rib, French Terry',
                'lead_time_days' => 45,
                'minimum_order_value' => 5000,
                'sync_status' => 'synced',
                'is_active' => true,
            ],
            [
                'company' => 'Global Fabrics Inc',
                'legal_name' => 'Global Fabrics International Inc.',
                'contact_name' => 'Maria Santos',
                'email' => 'maria@globalfabrics.com',
                'phone' => '+1 213 555 9999',
                'country' => 'United States',
                'city' => 'Los Angeles',
                'currency' => 'USD',
                'payment_terms' => 'Net 30',
                'supplier_category' => 'Fabric',
                'capabilities' => 'Cotton, Polyester, Blends',
                'lead_time_days' => 30,
                'minimum_order_value' => 3000,
                'sync_status' => 'synced',
                'is_active' => true,
            ],
            [
                'company' => 'Confecciones del Sur',
                'legal_name' => 'Confecciones del Sur S.A.',
                'contact_name' => 'Carlos Rodriguez',
                'email' => 'carlos@confeccionesdelsur.com',
                'phone' => '+54 11 4444 5555',
                'country' => 'Argentina',
                'city' => 'Buenos Aires',
                'currency' => 'USD',
                'payment_terms' => 'Net 45',
                'supplier_category' => 'CMT',
                'capabilities' => 'Cut, Make, Trim, Embroidery',
                'lead_time_days' => 60,
                'minimum_order_value' => 2000,
                'sync_status' => 'synced',
                'is_active' => true,
            ],
            [
                'company' => 'America Textil',
                'legal_name' => 'America Textil S.A. de C.V.',
                'contact_name' => 'Ana Martinez',
                'email' => 'ana@americatextil.com',
                'phone' => '+52 55 6666 7777',
                'country' => 'Mexico',
                'city' => 'Mexico City',
                'currency' => 'USD',
                'payment_terms' => 'Net 30',
                'supplier_category' => 'Full Package',
                'capabilities' => 'Full production, Screen Print, Embroidery',
                'lead_time_days' => 50,
                'minimum_order_value' => 4000,
                'sync_status' => 'synced',
                'is_active' => true,
            ],
        ];

        foreach ($suppliers as $supplier) {
            Supplier::create($supplier);
        }
    }
}
