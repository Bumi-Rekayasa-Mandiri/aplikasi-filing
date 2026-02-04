import { ImgHTMLAttributes } from "react"

type Props = ImgHTMLAttributes<HTMLImageElement>

export default function ApplicationLogo(props: Props) {
    return (
        <img
            src="images/BRM.jpeg"
            alt="Application Logo"
            className="h-20 w-30"
            {...props}
        />
    )
}