<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Exhibit;
use App\Models\ExhibitFloorPlan;
use App\Models\ARAnchor;
use App\Models\InteractiveSession;
use App\Models\Notification;
use App\Models\LocationQRCode;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * Single Exhibition Setup:
     * - 1 Exhibition: Technology Innovation Center
     * - 1 Floor Plan: Level 1 (floor_level=1 to match "Level 1.fbx")
     * - AR Anchors: placeholder positions (replace with FBX Locator Extractor output)
     * - 1 Test QR Code: XPONAV-TEST-0001 for manual entry testing
     */
    public function run(): void
    {
        // ═══════════════════════════════════════
        // USERS
        // ═══════════════════════════════════════
        $testUser = User::create([
            'name' => 'John Doe',
            'username' => 'johndoe',
            'email' => 'test@test.com',
            'password' => Hash::make('password123'),
            'email_verified_at' => now(),
            'profile_picture' => null,
            'image' => null,
            'phone' => '+1234567890',
            'phone_number' => '+1234567890',
            'role' => 'user',
            'status' => 'active',
            'membership_tier' => 'premium',
            'membership_expiry' => now()->addYear(),
            'is_admin' => false,
        ]);

        $adminUser = User::create([
            'name' => 'Admin User',
            'username' => 'admin',
            'email' => 'admin@xponav.com',
            'password' => Hash::make('admin123'),
            'email_verified_at' => now(),
            'role' => 'admin',
            'status' => 'active',
            'is_admin' => true,
        ]);

        // ═══════════════════════════════════════
        // SINGLE EXHIBITION
        // ═══════════════════════════════════════
        $exhibit1 = Exhibit::create([
            'name' => 'Technology Innovation Center',
            'title' => 'Tech Innovation Hub',
            'description' => 'Explore cutting-edge technology innovations including AI, robotics, VR/AR displays, and sustainable tech solutions. Perfect for tech enthusiasts and innovators.',
            'location' => 'Building A, Downtown Exhibition Complex',
            'latitude' => 37.7749,
            'longitude' => -122.4194,
            'opening_time' => '09:00:00',
            'closing_time' => '18:00:00',
            'ticket_price' => 25.00,
            'is_active' => true,
            'is_promoted' => true,
            'thumbnail_url' => 'https://picsum.photos/400/300?random=1',
            'rating' => 4.8,
        ]);

        // ═══════════════════════════════════════
        // SINGLE FLOOR PLAN — Level 1
        // floor_level=1 so TryLoadFloorModel(1) finds "FloorModels/Level 1"
        // ═══════════════════════════════════════
        $floorPlan = ExhibitFloorPlan::create([
            'exhibit_id' => $exhibit1->id,
            'name' => 'Level 1 - Main Floor',
            'floor_level' => 1,
            'model_file_path' => 'FloorModels/Level 1',
            'thumbnail_path' => null,
            'description' => 'Main exhibition floor with all exhibit zones',
            'width' => 50.0,
            'height' => 3.5,
            'length' => 40.0,
            'origin_latitude' => 37.7749,
            'origin_longitude' => -122.4194,
            'origin_altitude' => 0.0,
            'is_active' => true,
        ]);

        // ═══════════════════════════════════════
        // AR ANCHORS — Level 1
        //
        // NOTE: Replace these positions with FBX Locator Extractor output!
        // In Unity: Tools > FBX Locator Extractor > Extract > Copy PHP Seeder Code
        // Then paste below, replacing these placeholder anchors.
        //
        // These placeholder anchors form a basic navigation graph for testing.
        // ═══════════════════════════════════════
        // ═══ AR Anchors from FBX Level 1 — 53 locators ═══
        ARAnchor::create([
            'floor_plan_id' => $floorPlan->id,
            'exhibit_id' => $exhibit1->id,
            'anchor_name' => 'Level_1',
            'anchor_type' => 'navigation_point',
            'position_x' => 0.0000,
            'position_y' => 0.0000,
            'position_z' => 0.0000,
            'rotation_x' => 0.0,
            'rotation_y' => 0.0,
            'rotation_z' => 0.0,
            'rotation_w' => 1.0,
            'description' => 'Level_1 (from FBX)',
            'is_active' => true,
        ]);

        ARAnchor::create([
            'floor_plan_id' => $floorPlan->id,
            'exhibit_id' => $exhibit1->id,
            'anchor_name' => 'Bathrooms',
            'anchor_type' => 'exhibit_location',
            'position_x' => 0.0000,
            'position_y' => 0.0000,
            'position_z' => 0.0000,
            'rotation_x' => 0.0,
            'rotation_y' => 0.0,
            'rotation_z' => 0.0,
            'rotation_w' => 1.0,
            'description' => 'Bathrooms (from FBX)',
            'is_active' => true,
        ]);

        ARAnchor::create([
            'floor_plan_id' => $floorPlan->id,
            'exhibit_id' => $exhibit1->id,
            'anchor_name' => 'Bathroom_Hall_A',
            'anchor_type' => 'exhibit_location',
            'position_x' => 0.0000,
            'position_y' => 0.0000,
            'position_z' => 0.0000,
            'rotation_x' => 0.0,
            'rotation_y' => 0.0,
            'rotation_z' => 0.0,
            'rotation_w' => 1.0,
            'description' => 'Bathroom_Hall_A (from FBX)',
            'is_active' => true,
        ]);

        ARAnchor::create([
            'floor_plan_id' => $floorPlan->id,
            'exhibit_id' => $exhibit1->id,
            'anchor_name' => 'bathroom_Hall_B',
            'anchor_type' => 'exhibit_location',
            'position_x' => 0.0000,
            'position_y' => 0.0000,
            'position_z' => 0.0000,
            'rotation_x' => 0.0,
            'rotation_y' => 0.0,
            'rotation_z' => 0.0,
            'rotation_w' => 1.0,
            'description' => 'bathroom_Hall_B (from FBX)',
            'is_active' => true,
        ]);

        ARAnchor::create([
            'floor_plan_id' => $floorPlan->id,
            'exhibit_id' => $exhibit1->id,
            'anchor_name' => 'Bathroom_Hall_C',
            'anchor_type' => 'exhibit_location',
            'position_x' => 0.0000,
            'position_y' => 0.0000,
            'position_z' => 0.0000,
            'rotation_x' => 0.0,
            'rotation_y' => 0.0,
            'rotation_z' => 0.0,
            'rotation_w' => 1.0,
            'description' => 'Bathroom_Hall_C (from FBX)',
            'is_active' => true,
        ]);

        ARAnchor::create([
            'floor_plan_id' => $floorPlan->id,
            'exhibit_id' => $exhibit1->id,
            'anchor_name' => 'Bathroom_Hall_D',
            'anchor_type' => 'exhibit_location',
            'position_x' => -196.3544,
            'position_y' => 12.7211,
            'position_z' => 31.7721,
            'rotation_x' => 0.0,
            'rotation_y' => 180.0,
            'rotation_z' => 0.0,
            'rotation_w' => 1.0,
            'description' => 'Bathroom_Hall_D (from FBX)',
            'is_active' => true,
        ]);

        ARAnchor::create([
            'floor_plan_id' => $floorPlan->id,
            'exhibit_id' => $exhibit1->id,
            'anchor_name' => 'Corridors',
            'anchor_type' => 'navigation_point',
            'position_x' => 0.0000,
            'position_y' => 0.0000,
            'position_z' => 0.0000,
            'rotation_x' => 0.0,
            'rotation_y' => 0.0,
            'rotation_z' => 0.0,
            'rotation_w' => 1.0,
            'description' => 'Corridors (from FBX)',
            'is_active' => true,
        ]);

        ARAnchor::create([
            'floor_plan_id' => $floorPlan->id,
            'exhibit_id' => $exhibit1->id,
            'anchor_name' => 'Elevations',
            'anchor_type' => 'navigation_point',
            'position_x' => 0.0000,
            'position_y' => 0.0000,
            'position_z' => 0.0000,
            'rotation_x' => 0.0,
            'rotation_y' => 0.0,
            'rotation_z' => 0.0,
            'rotation_w' => 1.0,
            'description' => 'Elevations (from FBX)',
            'is_active' => true,
        ]);

        ARAnchor::create([
            'floor_plan_id' => $floorPlan->id,
            'exhibit_id' => $exhibit1->id,
            'anchor_name' => 'East_Lobby',
            'anchor_type' => 'navigation_point',
            'position_x' => 0.0000,
            'position_y' => 0.0000,
            'position_z' => 0.0000,
            'rotation_x' => 0.0,
            'rotation_y' => 0.0,
            'rotation_z' => 0.0,
            'rotation_w' => 1.0,
            'description' => 'East_Lobby (from FBX)',
            'is_active' => true,
        ]);

        ARAnchor::create([
            'floor_plan_id' => $floorPlan->id,
            'exhibit_id' => $exhibit1->id,
            'anchor_name' => 'Grand_Lobby',
            'anchor_type' => 'navigation_point',
            'position_x' => 0.0000,
            'position_y' => 0.0000,
            'position_z' => 0.0000,
            'rotation_x' => 0.0,
            'rotation_y' => 0.0,
            'rotation_z' => 0.0,
            'rotation_w' => 1.0,
            'description' => 'Grand_Lobby (from FBX)',
            'is_active' => true,
        ]);

        ARAnchor::create([
            'floor_plan_id' => $floorPlan->id,
            'exhibit_id' => $exhibit1->id,
            'anchor_name' => 'North_Lobby',
            'anchor_type' => 'navigation_point',
            'position_x' => 0.0000,
            'position_y' => 0.0000,
            'position_z' => 0.0000,
            'rotation_x' => 0.0,
            'rotation_y' => 0.0,
            'rotation_z' => 0.0,
            'rotation_w' => 1.0,
            'description' => 'North_Lobby (from FBX)',
            'is_active' => true,
        ]);

        ARAnchor::create([
            'floor_plan_id' => $floorPlan->id,
            'exhibit_id' => $exhibit1->id,
            'anchor_name' => 'Entrances',
            'anchor_type' => 'entrance',
            'position_x' => 0.0000,
            'position_y' => 0.0000,
            'position_z' => 0.0000,
            'rotation_x' => 0.0,
            'rotation_y' => 0.0,
            'rotation_z' => 0.0,
            'rotation_w' => 1.0,
            'description' => 'Entrances (from FBX)',
            'is_active' => true,
        ]);

        ARAnchor::create([
            'floor_plan_id' => $floorPlan->id,
            'exhibit_id' => $exhibit1->id,
            'anchor_name' => 'Halls',
            'anchor_type' => 'exhibit_location',
            'position_x' => 0.0000,
            'position_y' => 0.0000,
            'position_z' => 0.0000,
            'rotation_x' => 0.0,
            'rotation_y' => 0.0,
            'rotation_z' => 0.0,
            'rotation_w' => 1.0,
            'description' => 'Halls (from FBX)',
            'is_active' => true,
        ]);

        ARAnchor::create([
            'floor_plan_id' => $floorPlan->id,
            'exhibit_id' => $exhibit1->id,
            'anchor_name' => 'Room_HallA',
            'anchor_type' => 'exhibit_location',
            'position_x' => 0.0000,
            'position_y' => 0.0000,
            'position_z' => 0.0000,
            'rotation_x' => 0.0,
            'rotation_y' => 0.0,
            'rotation_z' => 0.0,
            'rotation_w' => 1.0,
            'description' => 'Room_HallA (from FBX)',
            'is_active' => true,
        ]);

        ARAnchor::create([
            'floor_plan_id' => $floorPlan->id,
            'exhibit_id' => $exhibit1->id,
            'anchor_name' => 'Room_HallB',
            'anchor_type' => 'exhibit_location',
            'position_x' => 0.0000,
            'position_y' => 0.0000,
            'position_z' => 0.0000,
            'rotation_x' => 0.0,
            'rotation_y' => 0.0,
            'rotation_z' => 0.0,
            'rotation_w' => 1.0,
            'description' => 'Room_HallB (from FBX)',
            'is_active' => true,
        ]);

        ARAnchor::create([
            'floor_plan_id' => $floorPlan->id,
            'exhibit_id' => $exhibit1->id,
            'anchor_name' => 'Room_HallC',
            'anchor_type' => 'exhibit_location',
            'position_x' => 0.0000,
            'position_y' => 0.0000,
            'position_z' => 0.0000,
            'rotation_x' => 0.0,
            'rotation_y' => 0.0,
            'rotation_z' => 0.0,
            'rotation_w' => 1.0,
            'description' => 'Room_HallC (from FBX)',
            'is_active' => true,
        ]);

        ARAnchor::create([
            'floor_plan_id' => $floorPlan->id,
            'exhibit_id' => $exhibit1->id,
            'anchor_name' => 'Room_HallD',
            'anchor_type' => 'exhibit_location',
            'position_x' => 0.0000,
            'position_y' => 0.0000,
            'position_z' => 0.0000,
            'rotation_x' => 0.0,
            'rotation_y' => 0.0,
            'rotation_z' => 0.0,
            'rotation_w' => 1.0,
            'description' => 'Room_HallD (from FBX)',
            'is_active' => true,
        ]);

        ARAnchor::create([
            'floor_plan_id' => $floorPlan->id,
            'exhibit_id' => $exhibit1->id,
            'anchor_name' => 'Shared_Walls',
            'anchor_type' => 'navigation_point',
            'position_x' => 0.0000,
            'position_y' => 0.0000,
            'position_z' => 0.0000,
            'rotation_x' => 0.0,
            'rotation_y' => 0.0,
            'rotation_z' => 0.0,
            'rotation_w' => 1.0,
            'description' => 'Shared_Walls (from FBX)',
            'is_active' => true,
        ]);

        ARAnchor::create([
            'floor_plan_id' => $floorPlan->id,
            'exhibit_id' => $exhibit1->id,
            'anchor_name' => 'Locators',
            'anchor_type' => 'navigation_point',
            'position_x' => 0.0000,
            'position_y' => 0.0000,
            'position_z' => 0.0000,
            'rotation_x' => 0.0,
            'rotation_y' => 0.0,
            'rotation_z' => 0.0,
            'rotation_w' => 1.0,
            'description' => 'Locators (from FBX)',
            'is_active' => true,
        ]);

        ARAnchor::create([
            'floor_plan_id' => $floorPlan->id,
            'exhibit_id' => $exhibit1->id,
            'anchor_name' => 'LO_Elevation_East_Lobby_Corridor',
            'anchor_type' => 'navigation_point',
            'position_x' => -33.8303,
            'position_y' => 1.5000,
            'position_z' => -136.1987,
            'rotation_x' => 0.0,
            'rotation_y' => 0.0,
            'rotation_z' => 0.0,
            'rotation_w' => 1.0,
            'description' => 'LO_Elevation_East_Lobby_Corridor (from FBX)',
            'is_active' => true,
        ]);

        ARAnchor::create([
            'floor_plan_id' => $floorPlan->id,
            'exhibit_id' => $exhibit1->id,
            'anchor_name' => 'LO_Elevation_East_Lobby_Corridor1',
            'anchor_type' => 'navigation_point',
            'position_x' => -89.3456,
            'position_y' => 1.5000,
            'position_z' => -135.9082,
            'rotation_x' => 0.0,
            'rotation_y' => 0.0,
            'rotation_z' => 0.0,
            'rotation_w' => 1.0,
            'description' => 'LO_Elevation_East_Lobby_Corridor1 (from FBX)',
            'is_active' => true,
        ]);

        ARAnchor::create([
            'floor_plan_id' => $floorPlan->id,
            'exhibit_id' => $exhibit1->id,
            'anchor_name' => 'LO_Elevation_East_Lobby_Corridor2',
            'anchor_type' => 'navigation_point',
            'position_x' => 43.1791,
            'position_y' => 1.5000,
            'position_z' => -140.4214,
            'rotation_x' => 0.0,
            'rotation_y' => 0.0,
            'rotation_z' => 0.0,
            'rotation_w' => 1.0,
            'description' => 'LO_Elevation_East_Lobby_Corridor2 (from FBX)',
            'is_active' => true,
        ]);

        ARAnchor::create([
            'floor_plan_id' => $floorPlan->id,
            'exhibit_id' => $exhibit1->id,
            'anchor_name' => 'LO_Elevation_GrandLobby1',
            'anchor_type' => 'navigation_point',
            'position_x' => -98.3640,
            'position_y' => 1.5000,
            'position_z' => 97.3396,
            'rotation_x' => 0.0,
            'rotation_y' => 0.0,
            'rotation_z' => 0.0,
            'rotation_w' => 1.0,
            'description' => 'LO_Elevation_GrandLobby1 (from FBX)',
            'is_active' => true,
        ]);

        ARAnchor::create([
            'floor_plan_id' => $floorPlan->id,
            'exhibit_id' => $exhibit1->id,
            'anchor_name' => 'LO_Elevation_GrandLobby_Ocean_ROom',
            'anchor_type' => 'exhibit_location',
            'position_x' => -4.3649,
            'position_y' => 1.5000,
            'position_z' => 92.7918,
            'rotation_x' => 0.0,
            'rotation_y' => 0.0,
            'rotation_z' => 0.0,
            'rotation_w' => 1.0,
            'description' => 'LO_Elevation_GrandLobby_Ocean_ROom (from FBX)',
            'is_active' => true,
        ]);

        ARAnchor::create([
            'floor_plan_id' => $floorPlan->id,
            'exhibit_id' => $exhibit1->id,
            'anchor_name' => 'LO_Elevation_Hall_A',
            'anchor_type' => 'exhibit_location',
            'position_x' => 60.6971,
            'position_y' => 1.5000,
            'position_z' => -7.4023,
            'rotation_x' => 0.0,
            'rotation_y' => 0.0,
            'rotation_z' => 0.0,
            'rotation_w' => 1.0,
            'description' => 'LO_Elevation_Hall_A (from FBX)',
            'is_active' => true,
        ]);

        ARAnchor::create([
            'floor_plan_id' => $floorPlan->id,
            'exhibit_id' => $exhibit1->id,
            'anchor_name' => 'LO_Elevation_North_Lobby_Corridor',
            'anchor_type' => 'navigation_point',
            'position_x' => 136.6444,
            'position_y' => 1.5000,
            'position_z' => -116.4827,
            'rotation_x' => 0.0,
            'rotation_y' => 0.0,
            'rotation_z' => 0.0,
            'rotation_w' => 1.0,
            'description' => 'LO_Elevation_North_Lobby_Corridor (from FBX)',
            'is_active' => true,
        ]);

        ARAnchor::create([
            'floor_plan_id' => $floorPlan->id,
            'exhibit_id' => $exhibit1->id,
            'anchor_name' => 'LO_Meeting_Rooms_Licon_Road',
            'anchor_type' => 'exhibit_location',
            'position_x' => -170.2218,
            'position_y' => 1.5000,
            'position_z' => 89.9486,
            'rotation_x' => 0.0,
            'rotation_y' => 0.0,
            'rotation_z' => 0.0,
            'rotation_w' => 1.0,
            'description' => 'LO_Meeting_Rooms_Licon_Road (from FBX)',
            'is_active' => true,
        ]);

        ARAnchor::create([
            'floor_plan_id' => $floorPlan->id,
            'exhibit_id' => $exhibit1->id,
            'anchor_name' => 'LO_Meeting_Rooms_Ocean_Drive1',
            'anchor_type' => 'exhibit_location',
            'position_x' => 45.3498,
            'position_y' => 1.5000,
            'position_z' => 92.8493,
            'rotation_x' => 0.0,
            'rotation_y' => 0.0,
            'rotation_z' => 0.0,
            'rotation_w' => 1.0,
            'description' => 'LO_Meeting_Rooms_Ocean_Drive1 (from FBX)',
            'is_active' => true,
        ]);

        ARAnchor::create([
            'floor_plan_id' => $floorPlan->id,
            'exhibit_id' => $exhibit1->id,
            'anchor_name' => 'LOC_Bathroom_Hall_A',
            'anchor_type' => 'exhibit_location',
            'position_x' => 63.4123,
            'position_y' => 1.5000,
            'position_z' => 17.1059,
            'rotation_x' => 0.0,
            'rotation_y' => 0.0,
            'rotation_z' => 0.0,
            'rotation_w' => 1.0,
            'description' => 'LOC_Bathroom_Hall_A (from FBX)',
            'is_active' => true,
        ]);

        ARAnchor::create([
            'floor_plan_id' => $floorPlan->id,
            'exhibit_id' => $exhibit1->id,
            'anchor_name' => 'LOC_Bathroom_Hall_B',
            'anchor_type' => 'exhibit_location',
            'position_x' => -47.2821,
            'position_y' => 1.5000,
            'position_z' => 39.3812,
            'rotation_x' => 0.0,
            'rotation_y' => 0.0,
            'rotation_z' => 0.0,
            'rotation_w' => 1.0,
            'description' => 'LOC_Bathroom_Hall_B (from FBX)',
            'is_active' => true,
        ]);

        ARAnchor::create([
            'floor_plan_id' => $floorPlan->id,
            'exhibit_id' => $exhibit1->id,
            'anchor_name' => 'LOC_Bathroom_Hall_C',
            'anchor_type' => 'exhibit_location',
            'position_x' => -79.3923,
            'position_y' => 1.5000,
            'position_z' => 41.3423,
            'rotation_x' => 0.0,
            'rotation_y' => 0.0,
            'rotation_z' => 0.0,
            'rotation_w' => 1.0,
            'description' => 'LOC_Bathroom_Hall_C (from FBX)',
            'is_active' => true,
        ]);

        ARAnchor::create([
            'floor_plan_id' => $floorPlan->id,
            'exhibit_id' => $exhibit1->id,
            'anchor_name' => 'LOC_Bathroom_Hall_D',
            'anchor_type' => 'exhibit_location',
            'position_x' => -185.1546,
            'position_y' => 1.5000,
            'position_z' => -15.2003,
            'rotation_x' => 0.0,
            'rotation_y' => 0.0,
            'rotation_z' => 0.0,
            'rotation_w' => 1.0,
            'description' => 'LOC_Bathroom_Hall_D (from FBX)',
            'is_active' => true,
        ]);

        ARAnchor::create([
            'floor_plan_id' => $floorPlan->id,
            'exhibit_id' => $exhibit1->id,
            'anchor_name' => 'LOC_East_lobby_to_Hall_Corridor',
            'anchor_type' => 'exhibit_location',
            'position_x' => -25.3249,
            'position_y' => 1.5000,
            'position_z' => -135.6257,
            'rotation_x' => 0.0,
            'rotation_y' => 0.0,
            'rotation_z' => 0.0,
            'rotation_w' => 1.0,
            'description' => 'LOC_East_lobby_to_Hall_Corridor (from FBX)',
            'is_active' => true,
        ]);

        ARAnchor::create([
            'floor_plan_id' => $floorPlan->id,
            'exhibit_id' => $exhibit1->id,
            'anchor_name' => 'LOC_EastLobby_Entrance',
            'anchor_type' => 'entrance',
            'position_x' => -62.0733,
            'position_y' => 1.5000,
            'position_z' => -135.5778,
            'rotation_x' => 0.0,
            'rotation_y' => 0.0,
            'rotation_z' => 0.0,
            'rotation_w' => 1.0,
            'description' => 'LOC_EastLobby_Entrance (from FBX)',
            'is_active' => true,
        ]);

        ARAnchor::create([
            'floor_plan_id' => $floorPlan->id,
            'exhibit_id' => $exhibit1->id,
            'anchor_name' => 'LOC_EastLobby_Hall_Entrance',
            'anchor_type' => 'entrance',
            'position_x' => -44.5311,
            'position_y' => 1.5000,
            'position_z' => -127.4795,
            'rotation_x' => 0.0,
            'rotation_y' => 0.0,
            'rotation_z' => 0.0,
            'rotation_w' => 1.0,
            'description' => 'LOC_EastLobby_Hall_Entrance (from FBX)',
            'is_active' => true,
        ]);

        ARAnchor::create([
            'floor_plan_id' => $floorPlan->id,
            'exhibit_id' => $exhibit1->id,
            'anchor_name' => 'LOC_GrandLobby_Entrance',
            'anchor_type' => 'entrance',
            'position_x' => -58.9285,
            'position_y' => 1.5000,
            'position_z' => 95.5297,
            'rotation_x' => 0.0,
            'rotation_y' => 0.0,
            'rotation_z' => 0.0,
            'rotation_w' => 1.0,
            'description' => 'LOC_GrandLobby_Entrance (from FBX)',
            'is_active' => true,
        ]);

        ARAnchor::create([
            'floor_plan_id' => $floorPlan->id,
            'exhibit_id' => $exhibit1->id,
            'anchor_name' => 'LOC_HAll_A',
            'anchor_type' => 'exhibit_location',
            'position_x' => 38.3939,
            'position_y' => 1.5000,
            'position_z' => -34.1206,
            'rotation_x' => 0.0,
            'rotation_y' => 0.0,
            'rotation_z' => 0.0,
            'rotation_w' => 1.0,
            'description' => 'LOC_HAll_A (from FBX)',
            'is_active' => true,
        ]);

        ARAnchor::create([
            'floor_plan_id' => $floorPlan->id,
            'exhibit_id' => $exhibit1->id,
            'anchor_name' => 'LOC_Hall_A_Entrance',
            'anchor_type' => 'entrance',
            'position_x' => 12.7668,
            'position_y' => 1.5000,
            'position_z' => 55.1424,
            'rotation_x' => 0.0,
            'rotation_y' => 0.0,
            'rotation_z' => 0.0,
            'rotation_w' => 1.0,
            'description' => 'LOC_Hall_A_Entrance (from FBX)',
            'is_active' => true,
        ]);

        ARAnchor::create([
            'floor_plan_id' => $floorPlan->id,
            'exhibit_id' => $exhibit1->id,
            'anchor_name' => 'LOC_Hall_A_to_lobby',
            'anchor_type' => 'exhibit_location',
            'position_x' => 12.7668,
            'position_y' => 1.5000,
            'position_z' => 75.4201,
            'rotation_x' => 0.0,
            'rotation_y' => 0.0,
            'rotation_z' => 0.0,
            'rotation_w' => 1.0,
            'description' => 'LOC_Hall_A_to_lobby (from FBX)',
            'is_active' => true,
        ]);

        ARAnchor::create([
            'floor_plan_id' => $floorPlan->id,
            'exhibit_id' => $exhibit1->id,
            'anchor_name' => 'LOC_Hall_B',
            'anchor_type' => 'exhibit_location',
            'position_x' => -30.3486,
            'position_y' => 1.5000,
            'position_z' => -34.1206,
            'rotation_x' => 0.0,
            'rotation_y' => 0.0,
            'rotation_z' => 0.0,
            'rotation_w' => 1.0,
            'description' => 'LOC_Hall_B (from FBX)',
            'is_active' => true,
        ]);

        ARAnchor::create([
            'floor_plan_id' => $floorPlan->id,
            'exhibit_id' => $exhibit1->id,
            'anchor_name' => 'LOC_Hall_B_Entrance',
            'anchor_type' => 'entrance',
            'position_x' => -4.6232,
            'position_y' => 1.5000,
            'position_z' => 54.9924,
            'rotation_x' => 0.0,
            'rotation_y' => 0.0,
            'rotation_z' => 0.0,
            'rotation_w' => 1.0,
            'description' => 'LOC_Hall_B_Entrance (from FBX)',
            'is_active' => true,
        ]);

        ARAnchor::create([
            'floor_plan_id' => $floorPlan->id,
            'exhibit_id' => $exhibit1->id,
            'anchor_name' => 'LOC_Hall_B_to_Lobby',
            'anchor_type' => 'exhibit_location',
            'position_x' => -4.6232,
            'position_y' => 1.5000,
            'position_z' => 75.2701,
            'rotation_x' => 0.0,
            'rotation_y' => 0.0,
            'rotation_z' => 0.0,
            'rotation_w' => 1.0,
            'description' => 'LOC_Hall_B_to_Lobby (from FBX)',
            'is_active' => true,
        ]);

        ARAnchor::create([
            'floor_plan_id' => $floorPlan->id,
            'exhibit_id' => $exhibit1->id,
            'anchor_name' => 'LOC_HAll_C',
            'anchor_type' => 'exhibit_location',
            'position_x' => -94.9499,
            'position_y' => 1.5000,
            'position_z' => -34.1206,
            'rotation_x' => 0.0,
            'rotation_y' => 0.0,
            'rotation_z' => 0.0,
            'rotation_w' => 1.0,
            'description' => 'LOC_HAll_C (from FBX)',
            'is_active' => true,
        ]);

        ARAnchor::create([
            'floor_plan_id' => $floorPlan->id,
            'exhibit_id' => $exhibit1->id,
            'anchor_name' => 'LOC_Hall_C_Entrance',
            'anchor_type' => 'entrance',
            'position_x' => -117.3833,
            'position_y' => 1.5000,
            'position_z' => 54.6339,
            'rotation_x' => 0.0,
            'rotation_y' => 0.0,
            'rotation_z' => 0.0,
            'rotation_w' => 1.0,
            'description' => 'LOC_Hall_C_Entrance (from FBX)',
            'is_active' => true,
        ]);

        ARAnchor::create([
            'floor_plan_id' => $floorPlan->id,
            'exhibit_id' => $exhibit1->id,
            'anchor_name' => 'LOC_Hall_C_to_Lobby',
            'anchor_type' => 'exhibit_location',
            'position_x' => -117.3833,
            'position_y' => 1.5000,
            'position_z' => 74.9116,
            'rotation_x' => 0.0,
            'rotation_y' => 0.0,
            'rotation_z' => 0.0,
            'rotation_w' => 1.0,
            'description' => 'LOC_Hall_C_to_Lobby (from FBX)',
            'is_active' => true,
        ]);

        ARAnchor::create([
            'floor_plan_id' => $floorPlan->id,
            'exhibit_id' => $exhibit1->id,
            'anchor_name' => 'LOC_HAll_D',
            'anchor_type' => 'exhibit_location',
            'position_x' => -170.6496,
            'position_y' => 1.5000,
            'position_z' => -34.1206,
            'rotation_x' => 0.0,
            'rotation_y' => 0.0,
            'rotation_z' => 0.0,
            'rotation_w' => 1.0,
            'description' => 'LOC_HAll_D (from FBX)',
            'is_active' => true,
        ]);

        ARAnchor::create([
            'floor_plan_id' => $floorPlan->id,
            'exhibit_id' => $exhibit1->id,
            'anchor_name' => 'LOC_Hall_D_Entrance',
            'anchor_type' => 'entrance',
            'position_x' => -135.5266,
            'position_y' => 1.5000,
            'position_z' => 56.0222,
            'rotation_x' => 0.0,
            'rotation_y' => 0.0,
            'rotation_z' => 0.0,
            'rotation_w' => 1.0,
            'description' => 'LOC_Hall_D_Entrance (from FBX)',
            'is_active' => true,
        ]);

        ARAnchor::create([
            'floor_plan_id' => $floorPlan->id,
            'exhibit_id' => $exhibit1->id,
            'anchor_name' => 'LOC_Hall_D_to_Lobby',
            'anchor_type' => 'exhibit_location',
            'position_x' => -135.5266,
            'position_y' => 1.5000,
            'position_z' => 76.2999,
            'rotation_x' => 0.0,
            'rotation_y' => 0.0,
            'rotation_z' => 0.0,
            'rotation_w' => 1.0,
            'description' => 'LOC_Hall_D_to_Lobby (from FBX)',
            'is_active' => true,
        ]);

        ARAnchor::create([
            'floor_plan_id' => $floorPlan->id,
            'exhibit_id' => $exhibit1->id,
            'anchor_name' => 'LOC_lobby_to_Lincon_road_rooms',
            'anchor_type' => 'exhibit_location',
            'position_x' => -169.9883,
            'position_y' => 1.5000,
            'position_z' => 103.1066,
            'rotation_x' => 0.0,
            'rotation_y' => 0.0,
            'rotation_z' => 0.0,
            'rotation_w' => 1.0,
            'description' => 'LOC_lobby_to_Lincon_road_rooms (from FBX)',
            'is_active' => true,
        ]);

        ARAnchor::create([
            'floor_plan_id' => $floorPlan->id,
            'exhibit_id' => $exhibit1->id,
            'anchor_name' => 'LOC_lobby_to_ocean_drive_rooms',
            'anchor_type' => 'exhibit_location',
            'position_x' => 43.3925,
            'position_y' => 1.5000,
            'position_z' => 103.1066,
            'rotation_x' => 0.0,
            'rotation_y' => 0.0,
            'rotation_z' => 0.0,
            'rotation_w' => 1.0,
            'description' => 'LOC_lobby_to_ocean_drive_rooms (from FBX)',
            'is_active' => true,
        ]);

        ARAnchor::create([
            'floor_plan_id' => $floorPlan->id,
            'exhibit_id' => $exhibit1->id,
            'anchor_name' => 'LOC_NorthLobby_Entrance',
            'anchor_type' => 'entrance',
            'position_x' => 138.7308,
            'position_y' => 1.5000,
            'position_z' => -125.9470,
            'rotation_x' => 0.0,
            'rotation_y' => 0.0,
            'rotation_z' => 0.0,
            'rotation_w' => 1.0,
            'description' => 'LOC_NorthLobby_Entrance (from FBX)',
            'is_active' => true,
        ]);

        ARAnchor::create([
            'floor_plan_id' => $floorPlan->id,
            'exhibit_id' => $exhibit1->id,
            'anchor_name' => 'Meeting_Rooms',
            'anchor_type' => 'exhibit_location',
            'position_x' => 0.0000,
            'position_y' => 0.0000,
            'position_z' => 0.0000,
            'rotation_x' => 0.0,
            'rotation_y' => 0.0,
            'rotation_z' => 0.0,
            'rotation_w' => 1.0,
            'description' => 'Meeting_Rooms (from FBX)',
            'is_active' => true,
        ]);

        ARAnchor::create([
            'floor_plan_id' => $floorPlan->id,
            'exhibit_id' => $exhibit1->id,
            'anchor_name' => 'Non_walkable_ARea',
            'anchor_type' => 'exhibit_location',
            'position_x' => 0.0000,
            'position_y' => 0.0000,
            'position_z' => 0.0000,
            'rotation_x' => 0.0,
            'rotation_y' => 0.0,
            'rotation_z' => 0.0,
            'rotation_w' => 1.0,
            'description' => 'Non_walkable_ARea (from FBX)',
            'is_active' => true,
        ]);


        // ═══════════════════════════════════════
        // TEST QR CODE — for manual entry testing
        // User types: XPONAV-TEST-0001
        // ═══════════════════════════════════════
        LocationQRCode::create([
            'code' => 'XPONAV-TEST-0001',
            'name' => 'Test QR - Main Entrance',
            'description' => 'Test QR code for manual entry. Type XPONAV-TEST-0001 in the QR scanner.',
            'exhibit_id' => $exhibit1->id,
            'floor_plan_id' => $floorPlan->id,
            'position_x' => 5.0,
            'position_y' => 0.0,
            'position_z' => 20.0,
            'rotation_y' => 0,
            'is_active' => true,
        ]);

        // ═══════════════════════════════════════
        // INTERACTIVE SESSIONS (for UI testing)
        // ═══════════════════════════════════════
        InteractiveSession::create([
            'name' => 'AI Workshop: Introduction to Machine Learning',
            'description' => 'Learn the basics of machine learning and AI in this hands-on workshop.',
            'date' => now()->addDays(3)->toDateString(),
            'time' => '14:00:00',
            'location' => 'Building A, Room 201',
            'type' => 'workshop',
            'hosted_by' => 'Dr. Sarah Chen',
            'role' => 'participant',
        ]);

        InteractiveSession::create([
            'name' => 'Robotics Demo: Build Your First Robot',
            'description' => 'Hands-on robotics session where you\'ll build and program a simple robot.',
            'date' => now()->addDays(5)->toDateString(),
            'time' => '10:00:00',
            'location' => 'Building A, Lab 3',
            'type' => 'demo',
            'hosted_by' => 'Prof. James Wright',
            'role' => 'participant',
        ]);

        // ═══════════════════════════════════════
        // NOTIFICATIONS
        // ═══════════════════════════════════════
        Notification::create([
            'user_id' => $testUser->id,
            'title' => 'Welcome to XpoNav!',
            'message' => 'Thank you for joining XpoNav. Explore amazing exhibitions with AR navigation!',
            'type' => 'welcome',
            'is_read' => false,
        ]);

        Notification::create([
            'user_id' => $testUser->id,
            'title' => 'Technology Innovation Center is Open!',
            'message' => 'Visit the Tech Innovation Hub — AR navigation is ready for you.',
            'type' => 'new_exhibit',
            'related_id' => $exhibit1->id,
            'related_type' => 'exhibit',
            'is_read' => false,
        ]);

        // ═══════════════════════════════════════
        // SUMMARY
        // ═══════════════════════════════════════
        echo "\n✅ Database seeded successfully!\n\n";
        echo "═══ Single Exhibition Setup ═══\n";
        echo "Exhibition: Technology Innovation Center\n";
        echo "Floor Plan: Level 1 (floor_level=1)\n";
        echo "AR Anchors: 6 (placeholder — replace with FBX locator data)\n";
        echo "QR Code: XPONAV-TEST-0001 (for manual entry testing)\n";
        echo "Sessions: 2\n\n";
        echo "Test User: test@test.com / password123\n";
        echo "Admin: admin@xponav.com / admin123\n\n";
        echo "═══ Next Steps ═══\n";
        echo "1. In Unity: Tools > FBX Locator Extractor > Extract locators from Level 1\n";
        echo "2. Copy PHP Seeder Code and replace the anchor section above\n";
        echo "3. Re-run: php artisan migrate:fresh --seed\n";
        echo "4. Test QR: type XPONAV-TEST-0001 in manual entry\n\n";
    }
}
