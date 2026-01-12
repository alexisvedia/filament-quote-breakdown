<?php

namespace Database\Seeders;

use App\Models\Client;
use Illuminate\Database\Seeder;

class ClientSeeder extends Seeder
{
    public function run(): void
    {
        $clients = [
            [
                'company' => 'Fashion Retail Corp',
                'legal_name' => 'Fashion Retail Corporation Inc.',
                'tax_id' => 'US-EIN-123456789',
                'contact_name' => 'John Smith',
                'email' => 'john.smith@fashionretail.com',
                'phone' => '+1 (555) 123-4567',
                'website' => 'www.fashionretail.com',
                'country' => 'United States',
                'region' => 'California',
                'city' => 'Los Angeles',
                'address' => '123 Fashion Avenue, Suite 500',
                'postal_code' => '90001',
                'currency' => 'USD',
                'payment_terms' => 'Net 30',
                'credit_limit' => 500000.00,
                'client_category' => 'Premium',
                'erp_code' => 'ERP-FC-001',
                'sync_status' => 'synced',
            ],
            [
                'company' => 'Global Apparel Inc',
                'legal_name' => 'Global Apparel International Inc.',
                'tax_id' => 'UK-VAT-987654321',
                'contact_name' => 'Jane Doe',
                'email' => 'jane@globalapparel.com',
                'phone' => '+44 20 1234 5678',
                'website' => 'www.globalapparel.com',
                'country' => 'United Kingdom',
                'region' => 'England',
                'city' => 'London',
                'address' => '45 Oxford Street',
                'postal_code' => 'W1D 1BS',
                'currency' => 'GBP',
                'payment_terms' => 'Net 45',
                'credit_limit' => 350000.00,
                'client_category' => 'Standard',
                'erp_code' => 'ERP-GA-002',
                'sync_status' => 'synced',
            ],
            [
                'company' => 'Allsaints US Limited',
                'legal_name' => 'Allsaints US Limited LLC',
                'tax_id' => 'US-EIN-555666777',
                'contact_name' => 'Michael Brown',
                'email' => 'michael@allsaints.com',
                'phone' => '+1 555 987 6543',
                'website' => 'www.allsaints.com',
                'country' => 'United States',
                'region' => 'New York',
                'city' => 'New York',
                'address' => '512 Broadway',
                'postal_code' => '10012',
                'currency' => 'USD',
                'payment_terms' => 'Net 30',
                'credit_limit' => 750000.00,
                'client_category' => 'Premium',
                'erp_code' => 'ERP-AS-003',
                'sync_status' => 'synced',
            ],
        ];

        foreach ($clients as $client) {
            Client::updateOrCreate(
                ['email' => $client['email']],
                $client
            );
        }
    }
}
