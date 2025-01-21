type WPError = {
    code: string | number
    message: string
}

type AjaxAction = {
    action: string
    key: string
    nonce: string
}

type AjaxResponse<T> =
    | { success: true; data: T }
    | { success: false; data: WPError[] }

/**
 * T: initial type
 */
type RootProps<T extends object = object> = {
    initialState: T
}

type State = {
    currentStep: Step                     // Screen's process step ID.
    head: number                          // Term ID. Selected terms will be merged into this term.
    selected: Set<number>                 // Selected terms' ID numbers.
    taxonomy: string                      // Selected taxonomy string.
    taxonomies: { [key: string]: string } // All taxonomies.
    terms: Term[]                         // All terms of the selected taxonomy.
}

type Step =
    | 'taxonomy-select' // 1/4 Selecting taxonomy
    | 'term-select'     // 2/4 Choose terms to merge
    | 'merge-select'    // 3/4 Select header term, trim selected
    | 'merge-complete'  // Done

type Term = {
    term_id: number
    name: string
    slug: string
    count: number
    description: string
}

export type {
    AjaxAction,
    AjaxResponse,
    RootProps,
    State,
    Step,
    Term,
    WPError,
}
