import {cn} from '@/lib/utils.ts'
import {forwardRef, HTMLAttributes, PropsWithChildren} from 'react'

type Props = PropsWithChildren<HTMLAttributes<HTMLDivElement> & {
    selected?: boolean
}>

const TermBox = forwardRef<HTMLDivElement, Props>((props: Props, ref) => {
    const {children, className, selected, ...rest} = props

    return (
        <div
            className={cn(
                'sw-bg-gray-100 hover:sw-bg-gray-300',        // background
                'sw-border sw-border-solid sw-border-gray-400', // border
                'sw-text-md hover:sw-font-bold',                  // font
                'hover:sw-font-bold hover:sw-cursor-pointer',     // hover
                // If selected,
                selected && 'sw-bg-gray-300 sw-text-black sw-font-bold',
                className,
            )}
            ref={ref}
            {...rest}
        >
            {children}
        </div>
    )
})
TermBox.displayName = 'TermBox'

export default TermBox
export type {
    Props,
}
