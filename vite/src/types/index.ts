// Simple aliases
type GroupId = number
type TermId = number

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
    assignIndex: Map<TermId, GroupId>     // index: term id to group.
    currentStep: Step                     // Screen's process step ID.
    groupOrders: GroupId[]                // Group's order information.
    groups: Map<GroupId, Group>           // All group information.
    maxGroups: number                     // The number of maximum groups.
    nextGroupId: GroupId                  // The next group's ID. Do not edit this value directly.
    selected: Set<TermId>                 // Selected terms' ID numbers.
    targetGroup: GroupId                  // Target group for merging.
    taxonomies: { [key: string]: string } // All taxonomies.
    taxonomy: string                      // Selected taxonomy string.
    termOrders: TermId[]                  // Order of all terms.
    terms: Map<TermId, Term>              // All terms of the selected taxonomy.
}

type Group = {
    id: GroupId
    description: string
    head: TermId
    isEdited: boolean
    isRenaming: boolean
    isSelected: boolean
    terms: TermId[]
    title: string
}

type Step =
    | 'taxonomy-select' // 1/2 Selecting taxonomy
    | 'term-merge'      // 2/2 Choose terms to merge

type Term = {
    term_id: TermId
    name: string
    slug: string
    count: number
    description: string
}

export type {
    AjaxAction,
    AjaxResponse,
    RootProps,
    GroupId,
    Group,
    State,
    Step,
    Term,
    TermId,
    WPError,
}
