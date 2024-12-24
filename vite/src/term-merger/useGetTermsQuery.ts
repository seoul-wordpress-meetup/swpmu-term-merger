import {adminAjax} from '@/lib/ajax.ts'
import queryKey from '@/lib/query-key.ts'
import {useGlobalContext} from '@/term-merger/context.tsx'
import {ActionType} from '@/term-merger/reducer.ts'
import {useQuery} from '@tanstack/react-query'
import {useEffect} from 'react'
import type {Term} from '@/types'

export default function useGetTermsQuery() {
    const {
        dispatch,
        state: {
            selected,
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
            dispatch({
                type: ActionType.SET_SELECTED,
                payload: filterSelected(selected, query.data),
            })
        }
    }, [query.data])

    return query
}

const filterSelected = (selected: Set<number>, terms: Term[]): Set<number> => {
    const newSelected = new Set<number>()

    // Filter that selected term id values are really in the data list.
    terms.forEach((term) => {
        if (selected.has(term.term_id)) {
            newSelected.add(term.term_id)
        }
    })

    return newSelected
}
