import { ImgHTMLAttributes } from "react"

type Props = ImgHTMLAttributes<HTMLImageElement>

export default function AppLogo(props: Props) {
    return (
        <img
            src="images/dashboard.jpg"
            alt="App Logo"
            className="h-20 w-30"
            {...props}
        />
    )
}