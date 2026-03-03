import DetailSKP from './DetailSKP'
import DetailSK from './DetailSK'
import DetailSPI  from './DetailSPI'
import DetailSPD  from './DetailSPD'
import DetailIEI  from './DetailIEI'
import DetailBRM1 from './DetailBRM1'
import DetailBRM2 from './DetailBRM2'
import DetailGRS  from './DetailGRS'
import DetailDefault from './DetailDefault'

const map: Record<string, React.ComponentType<{ surat: any }>> = {
    'SPI-BRM': DetailSPI,
    'SPD-BRM': DetailSPD,
    'IEI-BRM': DetailIEI,
    'BRM-1':   DetailBRM1,
    'BRM-2':   DetailBRM2,
    'GRS-BRM': DetailGRS,
    'SKP-BRM': DetailSKP,
    'SK-BRM': DetailSK,
}

export default function DetailRouter({ surat }: { surat: any }) {
    const Component = map[surat.jenis] ?? DetailDefault
    return <Component surat={surat} />
}