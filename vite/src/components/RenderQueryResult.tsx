import {UseQueryResult} from '@tanstack/react-query'
import {PropsWithChildren, ReactElement} from 'react'
import {__} from '@wordpress/i18n'

type Props = PropsWithChildren<{
    query: UseQueryResult<unknown>,
    onError?: () => ReactElement,
    onLoading?: () => ReactElement,
}>

export default function RenderQueryResult(props: Props) {
    const {
        children,
        query,
        onError,
        onLoading,
    } = props

    if (query.isLoading) {
        if (onLoading) {
            return onLoading()
        } else {
            return (
                <p className="sw-builtin-loading">
                    {__('Loaing now...', 'swpmu-term-merger')}
                </p>
            )
        }
    }

    if (query.isError) {
        if (onError) {
            return onError()
        } else {
            return (
                <p className="sw-builtin-error">
                    {__('Error: ', 'swpmu-term-merger')}
                    {query.error.message}
                </p>
            )
        }
    }

    if (query.isSuccess) {
        return <>{children}</>
    }

    return <></>
}
