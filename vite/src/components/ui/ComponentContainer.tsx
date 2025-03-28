import {cn} from '@/lib/utils.ts'
import React from 'react'

const ComponentContainer = React.forwardRef<HTMLDivElement, React.HTMLAttributes<HTMLDivElement>>(
    ({className, children, ...props}, ref) => (
        <div
            className={cn('sw-w-full', className)}
            ref={ref}
            {...props}
        >
            {children}
        </div>
    ))
ComponentContainer.displayName = 'ComponentContainer'

export default ComponentContainer
