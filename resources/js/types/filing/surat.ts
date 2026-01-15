export interface FileUpload {
  id: number
  original_name: string
  file_path: string
}

export interface NomorSuratLog {
  id: number
  nomor_surat: string
  tahun: number
  bulan: number
  kode_jenis: string
}

export interface Surat {
  id: number
  judul: string
  perihal: string
  tujuan: string
  isi_surat: string
  nomor_surat: string
  tanggal_surat: string
  status: 'draft' | 'final'
  nomor_surat_logs?: NomorSuratLog[]
}