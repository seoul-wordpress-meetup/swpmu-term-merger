import {createNewGroup, getDefaultState, getGroupByTermId} from '@/lib/utils.ts'
import {type Group, type GroupId, type State, type Step, type Term, TermId} from '@/types'
import {useReducer} from 'react'

enum ActionType {
    ADD_GROUP = 'addGroup',
    ASSIGN_GROUP = 'assignGroup',
    REMOVE_GROUP = 'removeGroup',
    SET_CURRENT_STEP = 'setCurrentStep',
    SET_GROUP = 'setGroup',
    SET_SELECTED = 'setSelected',
    SET_TARGET_GROUP = 'setTargetGroup',
    SET_TAXONOMY = 'setTaxonomy',
    SET_TERMS = 'setTerms',
}

type Action =
    | { type: ActionType.ADD_GROUP, payload?: undefined }
    | { type: ActionType.ASSIGN_GROUP, payload: { groupId: GroupId, termId: TermId } }
    | { type: ActionType.REMOVE_GROUP, payload: GroupId }
    | { type: ActionType.SET_CURRENT_STEP; payload: Step }
    | { type: ActionType.SET_GROUP; payload: Partial<Group> }
    | { type: ActionType.SET_SELECTED; payload: { termId: TermId; value: boolean } }
    | { type: ActionType.SET_TARGET_GROUP; payload: GroupId }
    | { type: ActionType.SET_TAXONOMY; payload: string }
    | { type: ActionType.SET_TERMS; payload: Term[] }

function reducer(prevState: State, action: Action): State {
    const {payload, type} = action

    switch (type) {
        case ActionType.ADD_GROUP:
            return (() => {
                const newGroups = new Map(prevState.groups),
                    newOrder = [...prevState.groupOrders],
                    newGroup = createNewGroup(prevState.nextGroupId)

                newGroups.set(newGroup.id, newGroup)
                newOrder.push(newGroup.id)

                return {
                    ...prevState,
                    nextGroupId: prevState.nextGroupId + 1,
                    groups: newGroups,
                    groupOrders: newOrder,
                }
            })()

        case ActionType.ASSIGN_GROUP:
            return (() => {
                const newAssignIndex = new Map(prevState.assignIndex),
                    newGroups = new Map(prevState.groups),
                    {groupId, termId} = payload

                // Remove term id from the already assigned group.
                const oldGroup = getGroupByTermId(termId, newGroups)
                if (oldGroup) {
                    const pos = oldGroup.terms.indexOf(termId)
                    if (pos > -1) {
                        oldGroup.terms.splice(pos, 1)
                    }
                }

                // Assign term id to new group.
                if (newGroups.has(groupId)) {
                    const newGroup = newGroups.get(groupId)!
                    // Make sure the group does not include the term.
                    if (!newGroup.terms.includes(termId)) {
                        newGroup.terms.push(termId)
                    }

                    // Sort orders by term name.
                    newGroup.terms = newGroup.terms.map((termId: number) => {
                        const term = prevState.terms.get(termId)
                        return (term ? [termId, term.name] : null) as ([number, string] | null)
                    }).filter((value) => {
                        return !!value
                    }).sort((a: [number, string], b: [number, string]): number => {
                        return a[1].localeCompare(b[1])
                    }).map(([termId, _]) => {
                        return termId
                    })
                }

                // Update assign map.
                newAssignIndex.set(termId, groupId)

                return {
                    ...prevState,
                    assignIndex: newAssignIndex,
                    groups: newGroups,
                }
            })()

        case ActionType.REMOVE_GROUP:
            return (() => {
                const newAssignIndex = new Map(prevState.assignIndex),
                    newGroups = new Map(prevState.groups),
                    newOrder = [...prevState.groupOrders],
                    pos = newOrder.indexOf(payload)

                // Update index
                newAssignIndex.forEach((value, key, map) => {
                    if (value === payload) {
                        map.delete(key)
                    }
                })

                if (newGroups.has(payload)) {
                    newGroups.delete(payload)
                }

                if (pos > -1) {
                    newOrder.splice(pos, 1)
                }

                return {
                    ...prevState,
                    assignIndex: newAssignIndex,
                    groups: newGroups,
                    groupOrder: newOrder,
                }
            })()

        case ActionType.SET_CURRENT_STEP:
            return {
                ...prevState,
                currentStep: payload,
            }

        case ActionType.SET_GROUP:
            return (() => {
                if (payload.id && prevState.groups.has(payload.id)) {
                    const nextAssignIndex = new Map(prevState.assignIndex),
                        nextGroups = new Map(prevState.groups),
                        theGroup = nextGroups.get(payload.id)!

                    // Sort orders by term name.
                    theGroup.terms = theGroup.terms.map((termId: number) => {
                        const term = prevState.terms.get(termId)
                        return (term ? [termId, term.name] : null) as ([number, string] | null)
                    }).filter((value) => {
                        return !!value
                    }).sort((a: [number, string], b: [number, string]): number => {
                        return a[1].localeCompare(b[1])
                    }).map(([termId, _]) => {
                        return termId
                    })

                    nextGroups.set(payload.id, {
                        ...theGroup,
                        ...payload,
                        isEdited: true,
                    })

                    // Update index
                    theGroup.terms.forEach((termId) => {
                        nextAssignIndex.set(termId, theGroup.id)
                    })

                    return {
                        ...prevState,
                        assignIndex: nextAssignIndex,
                        groups: nextGroups,
                    }
                }

                return prevState
            })()

        case ActionType.SET_SELECTED:
            return (() => {
                const {termId, value} = payload,
                    selected = new Set<TermId>(prevState.selected)

                if (value) {
                    selected.add(termId)
                } else {
                    selected.delete(termId)
                }

                return {
                    ...prevState,
                    selected,
                }
            })()

        case ActionType.SET_TARGET_GROUP:
            return {
                ...prevState,
                targetGroup: payload,
            }

        case ActionType.SET_TAXONOMY:
            return {
                ...prevState,
                taxonomy: payload,
            }

        case ActionType.SET_TERMS:
            return (() => {
                const {terms, termOrders} = filterTerms(payload)
                return {
                    ...prevState,
                    terms,
                    termOrders,
                }
            })()

        default:
            return prevState
    }
}

const useGlobalReducer = (initialState: Partial<State> = {}) => {
    const {
        groups: rawGroups,
        selected: rawSelected,
        ...restInitialState
    } = initialState

    let nextGroupId = initialState.nextGroupId || 1

    // Convert rawGroups
    const assignIndex = new Map<TermId, GroupId>,
        groupOrders: GroupId[] = [],
        groups = new Map<GroupId, Group>()
    // Be careful!
    rawGroups?.forEach((rawGroup) => {
        const group = createNewGroup(rawGroup.id, rawGroup)
        groups.set(group.id, group)
        // Update assignIndex
        group.terms.forEach((termId) => {
            assignIndex.set(termId, group.id)
        })
        // Assign orders
        groupOrders.push(group.id)
        // Modify nextGroupId
        nextGroupId = Math.max(nextGroupId, group.id) + 1
    })

    // Convert rawSelected
    const selected = new Set<TermId>(rawSelected || [])

    return useReducer<State, [action: Action]>(reducer, {
        ...getDefaultState(),
        ...restInitialState,
        assignIndex,
        groupOrders,
        groups,
        nextGroupId,
        selected,
    })
}

function filterTerms(terms: Term[]): {
    terms: Map<number, Term>
    termOrders: number[]
} {
    return {
        terms: new Map<number, Term>(
            terms.map((term) => [term.term_id, term]),
        ),
        termOrders: terms.map((term) => term.term_id),
    }
}

export type {
    Action,
}

export {
    ActionType,
    reducer,
    useGlobalReducer,
}
