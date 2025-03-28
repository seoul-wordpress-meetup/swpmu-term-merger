import {adminAjax} from '@/lib/ajax.ts'
import queryKey from '@/lib/query-key.ts'
import {useGlobalContext} from '@/term-merger/context.tsx'
import {ActionType} from '@/term-merger/reducer.ts'
import {useQuery} from '@tanstack/react-query'
import {useEffect} from 'react'

export default function useGetTermsQuery() {
    const {
        dispatch,
        state: {
            taxonomy,
        },
    } = useGlobalContext()

    const query = useQuery({
        queryKey: queryKey.getTerms(taxonomy),
        queryFn: () => adminAjax.getTerms(taxonomy),
        enabled: taxonomy.length > 0,
    })

    useEffect(() => {
        if (query.data) {
            dispatch({
                type: ActionType.SET_TERMS,
                payload: query.data,
            })
        }
    }, [query.data])

    return query
}
