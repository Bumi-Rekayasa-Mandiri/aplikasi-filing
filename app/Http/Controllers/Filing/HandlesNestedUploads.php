<?php

/*
|==========================================================================
| INSTRUKSI INTEGRASI
|==========================================================================
| Pada SuratController_update_patch.php, method syncTtds() dan syncPekerjaKtp()
| menerima parameter $ttdsFiles dan $pekerjaFiles. Cara mengakses files dari
| nested multipart request bisa tricky. Berikut cara paling reliable:
|
| Di method update(), ganti pemanggilan:
|
|   $this->syncTtds($surat,
|       $request->input('ttds', []),
|       $request->file('ttds') ?? []
|   );
|
| Tapi $request->file('ttds') bisa return array yang structure-nya:
|   [0 => ['file' => UploadedFile], 1 => ['file' => null], ...]
|
| atau:
|   ['0' => ['file' => UploadedFile], ...]
|
| Agar konsisten, gunakan helper berikut:
|==========================================================================
*/

namespace App\Http\Controllers\Filing;

use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;

trait HandlesNestedUploads
{
    /**
     * Ambil file dari nested multipart array.
     * Contoh: $this->getNestedFile($request, 'ttds', 0, 'file')
     *         akan ambil $request->file('ttds')[0]['file'] dengan aman.
     */
    protected function getNestedFile(Request $request, string $arrayKey, int $index, string $fieldKey): ?UploadedFile
    {
        $arr = $request->file($arrayKey);

        if (!is_array($arr)) return null;
        if (!isset($arr[$index])) return null;
        if (!is_array($arr[$index])) return null;
        if (!isset($arr[$index][$fieldKey])) return null;

        $file = $arr[$index][$fieldKey];

        return $file instanceof UploadedFile ? $file : null;
    }

    /**
     * Cek apakah index ke-N dari nested array punya file di field tertentu.
     */
    protected function hasNestedFile(Request $request, string $arrayKey, int $index, string $fieldKey): bool
    {
        return $this->getNestedFile($request, $arrayKey, $index, $fieldKey) !== null;
    }
}