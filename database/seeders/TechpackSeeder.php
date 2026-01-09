<?php

namespace Database\Seeders;

use App\Models\Techpack;
use Illuminate\Database\Seeder;

class TechpackSeeder extends Seeder
{
    public function run(): void
    {
        $techpacks = [
            [
                'style_code' => 'TP-001-2025',
                'style_name' => 'Classic T-Shirt Basic Line',
                'status' => 'approved',
                'client_id' => 1,
                'buyer' => 'Global Apparel Inc',
                'buyer_department' => 'Children',
                'product_group' => 'Tops',
                'sub_category' => 'T-Shirt',
                'our_contact' => 'John Smith',
                'season' => 'Fall 2025',
                'style_lead_time' => 45,
                'minimum_order_quantity' => 500,
                'construction' => 'Single Jersey 30/1',
                'content' => '100% Cotton',
                'weight' => '180 GSM',
                'dyeing_type' => 'Piece Dye',
                'colors' => ['Black', 'White', 'Navy'],
                'sizes' => ['XS', 'S', 'M', 'L', 'XL'],
                'base_size' => 'M',
                'factory_price' => 12.50,
                'profit_margin' => 20.00,
            ],
            [
                'style_code' => 'TP-002-2025',
                'style_name' => 'Premium Polo Shirt Corporate Line',
                'status' => 'approved',
                'client_id' => 1,
                'buyer' => 'Confecciones del Sur',
                'buyer_department' => 'Ladies',
                'buyer_style_reference' => 'NK-2025-SS-001',
                'product_group' => 'Tops',
                'sub_category' => 'Polo',
                'our_contact' => 'Maria Garcia',
                'season' => 'Summer 2026',
                'style_lead_time' => 60,
                'minimum_order_quantity' => 300,
                'style_embellishment' => true,
                'construction' => 'Pique 24/1',
                'content' => '100% Cotton',
                'weight' => '220 GSM',
                'dyeing_type' => 'Garment Dye',
                'colors' => ['White', 'Navy', 'Heather Gray'],
                'sizes' => ['S', 'M', 'L', 'XL', 'XXL'],
                'base_size' => 'L',
                'front_technique' => 'Embroidery',
                'back_technique' => 'Embroidery 3D',
                'factory_price' => 15.84,
                'profit_margin' => 19.00,
                'wfx_style_code' => 'STYLE-000001',
                'wfx_id' => 'WFX-51AA0B91',
            ],
            [
                'style_code' => 'TP-003-2025',
                'style_name' => 'Sport Hoodie Performance Series',
                'status' => 'under_review',
                'client_id' => 2,
                'buyer' => 'America Textil',
                'buyer_department' => 'Uniforms',
                'product_group' => 'Tops',
                'sub_category' => 'Hoodie',
                'our_contact' => 'David Lee',
                'season' => 'Fall 2025',
                'style_lead_time' => 75,
                'minimum_order_quantity' => 200,
                'style_embellishment' => true,
                'construction' => 'French Terry 20/1',
                'content' => '80% Cotton 20% Polyester',
                'weight' => '320 GSM',
                'dyeing_type' => 'Piece Dye',
                'colors' => ['Black', 'Gray', 'Navy', 'Red'],
                'sizes' => ['S', 'M', 'L', 'XL', 'XXL', '3XL'],
                'base_size' => 'L',
                'front_technique' => 'Screen Print',
                'sleeve_technique' => 'Vinyl',
                'factory_price' => 24.50,
                'profit_margin' => 22.00,
            ],
        ];

        foreach ($techpacks as $techpack) {
            Techpack::create($techpack);
        }
    }
}
