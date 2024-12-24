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
                'sw-bg-gray-100',                               // background
                'sw-border sw-border-solid sw-border-gray-400', // border
                'sw-m-1 sw-p-2',                                  // margin and padding
                'sw-text-md hover:sw-font-bold',                  // font
                'hover:sw-font-bold hover:sw-cursor-pointer',
                // If on hover
                selected ?
                    // hover on
                    'hover:sw-bg-gray-600' :
                    // hover off
                    'hover:sw-bg-gray-300',
                // If selected,
                selected ? // select
                    // selected
                    'sw-bg-gray-400 sw-text-black sw-font-bold hover:sw-text-white' :
                    // non-selected
                    '',
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