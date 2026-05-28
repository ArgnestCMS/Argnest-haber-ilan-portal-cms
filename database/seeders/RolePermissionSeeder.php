<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [

            // Panel
            ['name' => 'Panel Girişi', 'slug' => 'panel_giris'],

            // Haber
            ['name' => 'Haber Görüntüleme', 'slug' => 'haber_gor'],
            ['name' => 'Haber Ekleme', 'slug' => 'haber_ekle'],
            ['name' => 'Haber Düzenleme', 'slug' => 'haber_duzenle'],
            ['name' => 'Haber Silme', 'slug' => 'haber_sil'],

            // İlan
            ['name' => 'İlan Görüntüleme', 'slug' => 'ilan_gor'],
            ['name' => 'İlan Ekleme', 'slug' => 'ilan_ekle'],
            ['name' => 'İlan Düzenleme', 'slug' => 'ilan_duzenle'],
            ['name' => 'İlan Silme', 'slug' => 'ilan_sil'],

            // Video
            ['name' => 'Video Ekleme', 'slug' => 'video_ekle'],
            ['name' => 'Video Düzenleme', 'slug' => 'video_duzenle'],

            // Galeri
            ['name' => 'Galeri Ekleme', 'slug' => 'galeri_ekle'],
            ['name' => 'Galeri Düzenleme', 'slug' => 'galeri_duzenle'],

            // Yorum
            ['name' => 'Yorum Moderasyonu', 'slug' => 'yorum_moderasyonu'],

            // Kullanıcı
            ['name' => 'Kullanıcı Yönetimi', 'slug' => 'kullanici_yonet'],

            // Reklam
            ['name' => 'Reklam Yönetimi', 'slug' => 'reklam_yonet'],

             // Site
             ['name' => 'Site Ayarları', 'slug' => 'site_ayarlarini_yonet'],

             // SEO
             ['name' => 'SEO Yönetimi', 'slug' => 'seo_yonet'],

            // Forum
            ['name' => 'Forum Yönetimi', 'slug' => 'forum_yonet'],
            ['name' => 'Forum Moderasyonu', 'slug' => 'forum_moderasyonu'],

        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(
                ['slug' => $permission['slug']],
                $permission
            );
        }

        $admin = Role::firstOrCreate(
            ['slug' => 'admin'],
            [
                'name' => 'Admin',
                'color' => 'danger',
                'is_system' => true,
            ]
        );

        $editor = Role::firstOrCreate(
            ['slug' => 'editor'],
            [
                'name' => 'Editör',
                'color' => 'warning',
                'is_system' => true,
            ]
        );

        $moderator = Role::firstOrCreate(
            ['slug' => 'moderator'],
            [
                'name' => 'Moderatör',
                'color' => 'success',
                'is_system' => true,
            ]
        );

        $userRole = Role::firstOrCreate(
            ['slug' => 'user'],
            [
                'name' => 'Kullanıcı',
                'color' => 'gray',
                'is_system' => true,
            ]
        );

        $admin->permissions()->syncWithoutDetaching(
            Permission::pluck('id')
        );

        $editor->permissions()->syncWithoutDetaching(
            Permission::whereIn('slug', [
                'panel_giris',

                'haber_gor',
                'haber_ekle',
                'haber_duzenle',

                'ilan_gor',
                'ilan_ekle',
                'ilan_duzenle',

                'video_ekle',
                'video_duzenle',

                'galeri_ekle',
                'galeri_duzenle',
            ])->pluck('id')
        );

        $moderator->permissions()->syncWithoutDetaching(
            Permission::whereIn('slug', [
                'panel_giris',
                'yorum_moderasyonu',
                'forum_moderasyonu',
            ])->pluck('id')
        );

        User::query()->each(function (User $user) use (
            $admin,
            $editor,
            $moderator,
            $userRole
        ) {
            $roleId = match ($user->role) {
                'admin' => $admin->id,
                'editor' => $editor->id,
                'moderator' => $moderator->id,
                default => $userRole->id,
            };

            if (! $user->role_id) {
                $user->forceFill(['role_id' => $roleId])->save();
            }
        });
    }
}
