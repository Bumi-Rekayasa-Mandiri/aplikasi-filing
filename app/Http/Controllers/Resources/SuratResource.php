<?php

namespace App\Http\Resource;

class SuratResource extends Resource {
    
    public function toArray($request){
        return [
            'id' => $this->id,
            'nomor_surat' => $this->nomor_surat,
            'status' => $this->status,
            'has_file' => $this->hasMedia('surat_files'),
        ];
    }
}