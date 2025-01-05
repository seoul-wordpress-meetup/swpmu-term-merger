import {getDefaultState} from '@/lib/utils.ts'
import {State, Step, Term} from '@/types'
import {useReducer} from 'react'

enum ActionType {
    ADD_TERM = 'addTerm',
    REMOVE_TERM = 'removeTerm',
    RESET = 'reset',
    SET_CURRENT_STEP = 'setCurrentStep',
    SET_HEAD = 'setHead',
    SET_SELECTED = 'setSelected',
    SET_TAXONOMY = 'setTaxonomy',
    SET_TERMS = 'setTerms',
}

type Action =
    | { type: ActionType.ADD_TERM, payload: number }
    | { type: ActionType.REMOVE_TERM, payload: number }
    | { type: ActionType.RESET, payload: Step | undefined }
    | { type: ActionType.SET_CURRENT_STEP; payload: Step }
    | { type: ActionType.SET_HEAD; payload: number }
    | { type: ActionType.SET_SELECTED; payload: Set<number> | Array<number> }
    | { type: ActionType.SET_TAXONOMY; payload: string }
    | { type: ActionType.SET_TERMS; payload: Term[] }

function reducer(prevState: State, action: Action) {
    const {payload, type} = action

    switch (type) {
        case ActionType.ADD_TERM:
            return (() => {
                const nextTerms = new Set(prevState.selected)
                nextTerms.add(payload)

                return {
                    ...prevState,
                    selected: nextTerms,
                }
            })()

        case ActionType.REMOVE_TERM:
            return (() => {
                const nextTerms = new Set(prevState.selected)
                nextTerms.delete(payload)

                return {
                    ...prevState,
                    selected: nextTerms,
                }
            })()

        case ActionType.RESET:
            return {
                ...prevState,
                currentStep: payload ?? 'taxonomy-select',
                head: 0,
                selected: new Set<number>(),
                taxonomy: '',
                terms: [],
            }

        case ActionType.SET_SELECTED:
            return {
                ...prevState,
                selected: new Set(payload),
            }

        case ActionType.SET_CURRENT_STEP:
            return {
                ...prevState,
                currentStep: payload,
            }

        case ActionType.SET_HEAD:
            return {
                ...prevState,
                head: payload,
            }

        case ActionType.SET_TAXONOMY:
            return {
                ...prevState,
                taxonomy: payload,
            }

        case ActionType.SET_TERMS:
            return {
                ...prevState,
                terms: payload,
            }

        default:
            return prevState
    }
}

const useGlobalReducer = (initialState: Partial<State> = {}) => {
    const {selected, ...rest} = initialState

    return useReducer<State, [action: Action]>(reducer, {
        ...getDefaultState(),
        ...rest,
        selected: selected ? new Set<number>(selected) : new Set<number>(),
    })
}

export type {
    Action,
}

export {
    ActionType,
    reducer,
    useGlobalReducer,
}
