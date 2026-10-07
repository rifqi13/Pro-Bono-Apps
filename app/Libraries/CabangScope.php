<?php
namespace App\Libraries;

/**
 * Helper untuk membatasi query agar hanya mengakses data cabang tertentu.
 * Wajib dipakai di semua controller cabang.
 */
class CabangScope
{
    /**
     * Ambil ID cabang dari session, throw jika tidak ada.
     */
    public static function id(): int
    {
        $id = session()->get('cabang_id');
        if (!$id) {
            throw new \RuntimeException('Session cabang_id tidak ditemukan.');
        }
        return (int) $id;
    }

    /**
     * Terapkan scope cabang ke builder.
     */
    public static function apply($builder, string $column = 'pbh_cabang_id'): void
    {
        $builder->where($column, self::id());
    }

    /**
     * Cek apakah sebuah record milik cabang yang sedang login.
     * Throw 403 jika bukan.
     */
    public static function authorize(array $record, string $column = 'pbh_cabang_id'): void
    {
        if (!isset($record[$column]) || (int) $record[$column] !== self::id()) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Anda tidak berhak mengakses data ini.');
        }
    }
}