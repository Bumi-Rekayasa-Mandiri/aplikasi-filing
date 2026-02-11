export interface ArsipSertifikat {
    id: number;
    nama_sertifikat: string;
    nomor_sertifikat: string | null;
    jenis_sertifikat: string | null;
    instansi: string | null;
    deleted_at: string | null;
    created_at: string;
    updated_at: string;
}