export interface ArsipSurat {
    id: number;
    judul: string;
    nomor_surat: string | null;
    tahun: number | null;
    kategori: string | null;
    keterangan: string | null;
    created_at: string;
    updated_at: string;
}