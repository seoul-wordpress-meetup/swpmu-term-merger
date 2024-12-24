import React from 'react'
import {cn} from '@/lib/utils'

const Card = React.forwardRef<HTMLDivElement, React.HTMLAttributes<HTMLDivElement>>(
    ({className, children, ...props}, ref) => (
        <div
            className={cn(
                'sw-border sw-border-solid sw-border-gray-400', // border
                className,
            )}
            ref={ref}
            {...props}
        >
            {children}
        </div>
    ),
)
Card.displayName = 'Card'

const CardHeader = React.forwardRef<HTMLDivElement, React.HTMLAttributes<HTMLDivElement>>(
    ({className, children, ...props}, ref) => (
        <div
            className={cn(
                'sw-card-header',
                'sw-bg-gray-300 sw-p-2',
                className,
            )}
            ref={ref}
            {...props}
        >
            {children}
        </div>
    ),
)
CardHeader.displayName = 'CardHeader'

const CardHeaderLabel = React.forwardRef<HTMLHeadingElement, React.HTMLAttributes<HTMLHeadingElement>>(
    ({className, children, ...props}, ref) => (
        <h2 className={cn('sw-m-0', className)} ref={ref} {...props}>
            {children}
        </h2>
    )
)
CardHeaderLabel.displayName = 'CardHeaderLabel'

const CardBody = React.forwardRef<HTMLDivElement, React.HTMLAttributes<HTMLDivElement>>(
    ({className, children, ...props}, ref) => (
        <div
            className={cn(
                'sw-card-body',
                'sw-p-2',
                className,
            )}
            ref={ref}
            {...props}
        >
            {children}
        </div>
    )
)
CardBody.displayName = 'CardBody'

const CardBodyDescription = React.forwardRef<HTMLParagraphElement, React.HTMLAttributes<HTMLParagraphElement>>(
    ({className, children, ...props}, ref) => (
        <p
            className={cn(
                'sw-mx-0 sw-my-2 sw-px-2 sw-py-0',
                className,
            )}
            ref={ref}
            {...props}
        >
            {children}
        </p>
    )
)
CardBodyDescription.displayName = 'CardBodyDescription'

const CardFooter = React.forwardRef<HTMLDivElement, React.HTMLAttributes<HTMLDivElement>>(
    ({className, children, ...props}, ref) => (
        <div
            className={cn(
                'sw-card-footer',
                className,
            )}
            ref={ref}
            {...props}
        >
            {children}
        </div>
    )
)
CardFooter.displayName = 'CardFooter'


export {
    Card,
    CardHeader,
    CardHeaderLabel,
    CardBody,
    CardBodyDescription,
    CardFooter,
}