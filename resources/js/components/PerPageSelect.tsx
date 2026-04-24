type Props = {
  value: number
  onChange: (value: number) => void
}

const options = [10, 25, 50]

export default function PerPageSelect({ value, onChange }: Props) {
  return (
    <div style={{ display: 'flex', alignItems: 'center', gap: '8px' }}>
      <span style={{ fontSize: '12px', color: '#9ca3af', whiteSpace: 'nowrap' }}>
        Tampilkan
      </span>
      <div style={{ display: 'flex', gap: '4px' }}>
        {options.map(opt => (
          <button
            key={opt}
            type="button"
            onClick={() => onChange(opt)}
            style={{
              padding: '5px 10px',
              borderRadius: '6px',
              border: '0.5px solid',
              fontSize: '12px',
              fontWeight: 500,
              cursor: 'pointer',
              fontFamily: 'inherit',
              transition: 'all 0.12s',
              borderColor: value === opt ? '#185FA5' : 'rgba(0,0,0,0.15)',
              background: value === opt ? '#E6F1FB' : '#ffffff',
              color: value === opt ? '#0C447C' : '#6b7280',
            }}
          >
            {opt}
          </button>
        ))}
      </div>
      <span style={{ fontSize: '12px', color: '#9ca3af', whiteSpace: 'nowrap' }}>
        data
      </span>
    </div>
  )
}