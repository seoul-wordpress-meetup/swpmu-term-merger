import {__} from '@wordpress/i18n'
import {useGlobalContext} from '@/term-merger/context'
import {ActionType} from '@/term-merger/reducer'

export default function AddGroup() {
    const {
        dispatch,
        state: {
            groups,
            maxGroups,
            terms,
        },
    } = useGlobalContext()
    return (
        <button
            className="button button-secondary"
            disabled={maxGroups <= groups.size || terms.size === 0}
            onClick={(e) => {
                const target = e.target as HTMLButtonElement
                dispatch({
                    type: ActionType.ADD_GROUP,
                })
                target.blur()
            }}
            title={__('Add a new group', 'swpmu-term-merger')}
        >
            {maxGroups > groups.size ?
                __('Add Group', 'swpmu-term-merger') :
                __('Max. Reached', 'swpmu-term-merger')}
        </button>
    )
}