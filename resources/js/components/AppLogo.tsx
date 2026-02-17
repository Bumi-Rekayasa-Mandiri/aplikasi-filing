import { ImgHTMLAttributes } from "react"
import dashboard from '@/assets/dashboard.jpg'

// type Props = ImgHTMLAttributes<HTMLImageElement>

export default function AppLogo() {
  return (
    <div className="flex items-center gap-4">
      <img
        src={dashboard}
        alt="AppLogo"
        className="h-16 w-auto"
      />

      <div className="flex flex-col">
        <span className="text-white text-2xl font-bold">
          PT. Bumi Rekayasa Mandiri
        </span>
        <span className="text-white text-sm">
          Precision Building. Sustainable Value.
        </span>
      </div>
    </div>
  )
}