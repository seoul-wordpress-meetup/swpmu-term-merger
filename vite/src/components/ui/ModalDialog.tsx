import {cn} from '@/lib/utils.ts'
import React from 'react'

const ModalDialog = React.forwardRef<HTMLDialogElement, React.DialogHTMLAttributes<HTMLDialogElement>>(
    ({className, children, open, ...props}, ref) => (
        open && (
            <div
                className={cn(
                    'sw-w-full sw-h-full',
                    'sw-fixed sw-top-0 sw-left-0 sw-z-50',
                    'sw-bg-black/50',
                )}
            >
                <div className="sw-relative sw-w-full sw-h-full">
                    <dialog
                        aria-modal="true"
                        className={cn(
                            'sw-absolute sw-inset-0',
                            'sw-border-0 sw-shadow-3xl',
                            className,
                        )}
                        open={open}
                        ref={ref}
                        role="dialog"
                        {...props}
                    >
                        {children}
                    </dialog>
                </div>
            </div>
        )
    ))
ModalDialog.displayName = 'ModalDialog'

export default ModalDialog
