import axios from 'axios'
import { Surat } from '@/types/filing/surat'

export const suratApi = {
  list: (params?: any) =>
    axios.get('/api/surat', { params }),

  show: (id: number) =>
    axios.get<Surat>(`/api/surat/${id}`),

  create: (data: Partial<Surat>) =>
    axios.post('/api/surat', data),

  update: (id: number, data: Partial<Surat>) =>
    axios.put(`/api/surat/${id}`, data),

  upload: (id: number, form: FormData) =>
    axios.post(`/api/surat/${id}/upload`, form),
}