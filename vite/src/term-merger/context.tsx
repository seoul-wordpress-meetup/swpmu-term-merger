import {getDefaultState} from '@/lib/utils.ts'
import {State} from '@/types'
import {ActionDispatch, createContext, useContext} from 'react'
import {Action} from './reducer'

type TermMergerContext = {
    dispatch: ActionDispatch<[action: Action]>
    state: State
}

const Context = createContext<TermMergerContext>({
    dispatch: () => {
    },
    state: getDefaultState(),
})

const useGlobalContext = () => useContext(Context)

export type {
    TermMergerContext,
}

export {
    Context,
    useGlobalContext,
}
