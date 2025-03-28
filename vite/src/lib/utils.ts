import {type Group, type GroupId, type State, type Term, TermId} from '@/types'
import {__, sprintf} from '@wordpress/i18n'
import {type ClassValue, clsx} from 'clsx'
import {twMerge} from 'tailwind-merge'

export function cn(...inputs: ClassValue[]) {
    return twMerge(clsx(inputs))
}

export function getDefaultState(): State {
    return {
        assignIndex: new Map<TermId, GroupId>(),
        currentStep: 'taxonomy-select',
        maxGroups: 5,
        selected: new Set<TermId>(),
        groupOrders: [],
        groups: new Map<GroupId, Group>(),
        nextGroupId: 1,
        targetGroup: 0,
        taxonomies: {},
        taxonomy: '',
        terms: new Map<TermId, Term>(),
        termOrders: [],
    }
}

export function createNewGroup(groupId: number, data: Partial<Group> = {}): Group {
    const {id, ...restData} = data

    return {
        id: groupId,
        description: '',
        head: 0,
        isEdited: false,
        isRenaming: false,
        isSelected: false,
        terms: [],
        title: sprintf(__('Group #%d', 'swpmu-term-merger'), groupId),
        ...restData,
    }
}

export function getGroupByTermId(termId: number, groups: Map<GroupId, Group>): Group | undefined {
    for (const group of groups.values()) {
        if (group.terms.includes(termId)) {
            return group
        }
    }
}
