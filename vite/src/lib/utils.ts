import {State} from '@/types'
import {type ClassValue, clsx} from 'clsx'
import {twMerge} from 'tailwind-merge'

export function cn(...inputs: ClassValue[]) {
    return twMerge(clsx(inputs))
}

export function getDefaultState(): State {
    return {
        currentStep: 'taxonomy-select',
        head: 0,
        selected: new Set<number>(),
        taxonomies: {},
        taxonomy: '',
        terms: [],
    }
}
