import {cn} from '@/lib/utils'
import {useGlobalContext} from '@/term-merger/context'
import {ActionType} from '@/term-merger/reducer'
import type {Group, GroupId, Term} from '@/types'
import {__} from '@wordpress/i18n'
import {useState} from 'react'

type Props = {
    group: Group
}

export default function Group(props: Props) {
    const {
        state: {
            terms,
        },
    } = useGlobalContext()

    const {group} = props

    return (
        <div className={cn('swpmu-group')}>
            <GroupHeader group={group} />
            <ul
                id={`group-items-${group.id}`}
                className="swpmu-group-items sw-m-0 sw-p-2"
            >
                {group.terms.map((termId) => {
                    const term = terms.get(termId)

                    return (term && (
                        <li
                            className={cn(
                                'sw-group-item',
                                'sw-m-0 sw-mb-2 sw-p-2',
                                'sw-border sw-border-x-0 sw-border-solid sw-border-zinc-400',
                            )}
                            key={termId}
                        >
                            <GroupItem
                                key={term.term_id}
                                group={group}
                                term={term}
                            />
                        </li>
                    ))
                })}
            </ul>
            <GroupFooter group={group} />
        </div>
    )
}

type GroupHeaderProps = Props & {
    hovered?: boolean,
}

const GroupHeader = (props: GroupHeaderProps) => {
    const {dispatch} = useGlobalContext()

    const {
        group: {
            id,
            isEdited,
            isRenaming,
            title,
        },
    } = props

    return (
        <div>
            <div
                className={cn(
                    'sw-p-2 sw-bg-gray-300', // generic
                )}
            >
                <h3
                    className={cn(
                        'sw-m-0',
                        {
                            'sw-text-sm sw-font-normal': isRenaming,
                        },
                    )}
                >
                    {!isRenaming && title}
                    {isRenaming && (
                        <>
                            <label htmlFor="" className="sw-me-2">
                                {__('New Title', 'swpmu-term-merger')}:
                            </label>
                            <input
                                id={`new-title-${id}`}
                                className="text"
                                onKeyUp={(e) => {
                                    if (e.key === 'Enter') {
                                        const title = (e.target as HTMLInputElement).value.trim()
                                        dispatch({
                                            type: ActionType.SET_GROUP,
                                            payload: {
                                                id,
                                                isRenaming: false,
                                                title,
                                            },
                                        })
                                    }
                                }}
                                onChange={(e) => {
                                    const value = e.target.value as string
                                    dispatch({
                                        type: ActionType.SET_GROUP,
                                        payload: {
                                            id: id,
                                            title: value,
                                        },
                                    })
                                }}
                                type="text"
                                value={title}
                            />
                        </>
                    )}
                </h3>
            </div>

            {/* Upper tool area */}
            <div className="sw-p-2 sw-flex sw-justify-between sw-item-between">
                <div className="sw-font-bold">
                    {__('ID', 'swpmu-term-merger')}: {id}
                </div>
                <div>
                    <a
                        className="sw-no-underline"
                        href={'#'}
                        onClick={(e) => {
                            const target = e.target as HTMLAnchorElement
                            e.preventDefault()
                            dispatch({
                                type: ActionType.SET_GROUP,
                                payload: {
                                    id: id,
                                    isRenaming: !isRenaming,
                                },
                            })
                            target.blur()
                        }}
                        title={'Rename the title of this group'}
                    >
                        {!isRenaming && __('Rename', 'swpmu-term-merger')}
                        {!isRenaming || __('Done', 'swpmu-term-merger')}
                    </a>
                    {' '}|{' '}
                    <a
                        className={cn(
                            'sw-no-underline sw-text-red-600', // generic
                        )}
                        href={'#'}
                        onClick={(e) => {
                            const target = e.target as HTMLAnchorElement
                            e.preventDefault()
                            if (isEdited && !confirm('Are you sure you want to remove this group?')) {
                                target.blur()
                                return false
                            }
                            dispatch({
                                type: ActionType.REMOVE_GROUP,
                                payload: id,
                            })
                            target.blur()
                        }}
                        title={'Remove this group'}
                    >
                        {__('Remove', 'swpmu-term-merger')}
                    </a>
                </div>
            </div>
        </div>
    )
}

const GroupItem = ({group, term}: { group: Group; term: Term }) => {
    const {
        dispatch,
        state: {
            groups,
            groupOrders,
        },
    } = useGlobalContext()

    const [isReassigning, setIsReassigning] = useState<boolean>(false),
        [reAssignedGroupId, setReAssignedGroupId] = useState<GroupId>(0)

    return (
        <section className="">
            <p className="sw-m-0 sw-mb-2">
                {term.name}
            </p>
            <p
                className={cn(
                    'sw-m-0 sw-text-right',
                )}
            >
                {groups.size > 1 && (
                    <>
                        <a
                            className="sw-no-underline"
                            href={'#'}
                            onClick={(e) => {
                                const target = e.target as HTMLAnchorElement
                                e.preventDefault()
                                setIsReassigning(!isReassigning)
                                target.blur()
                            }}
                            title={'Move to another group'}
                        >
                            {isReassigning ? __('Cancel', 'swpmu-term-merger') : __('Re-assign', 'swpmu-term-merger')}
                        </a>
                        {' '}|{' '}
                    </>
                )}
                <a
                    className="sw-no-underline sw-text-red-600"
                    href={'#'}
                    onClick={(e) => {
                        const target = e.target as HTMLAnchorElement
                        e.preventDefault()
                        dispatch({
                            type: ActionType.SET_GROUP,
                            payload: {
                                id: group.id,
                                terms: group.terms.filter((termId) => termId !== term.term_id),
                            },
                        })
                        target.blur()
                    }}
                    title={__('Remove this term from the group', 'swpmu-term-merger')}
                >
                    {__('Remove', 'swpmu-term-merger')}
                </a>
            </p>
            {isReassigning && (
                <div
                    className={cn(
                        'sw-m-0 sw-mt-4 sw-pt-4',
                        'sw-border sw-border-solid sw-border-gray-700 sw-border-x-0 sw-border-b-0',
                    )}
                >
                    <label
                        className="sw-block sw-mb-2"
                        htmlFor={`group-re-assign-${term.term_id}`}
                    >
                        {__('Re-assign to', 'swpmu-term-merger')}:
                    </label>
                    <div className={cn('sw-flex sw-justify-between sw-item-center')}>
                        <select
                            className="sw-inline-block"
                            id={`group-re-assign-${term.term_id}`}
                            onChange={(e) => {
                                const value = parseInt(e.target.value)
                                setReAssignedGroupId(value)
                            }}
                            value={reAssignedGroupId}
                        >
                            <option value={0} disabled>
                                {__('Assign new group', 'swpmu-term-merger')}
                            </option>
                            {groupOrders.filter((id: GroupId) => id !== group.id).map((id) => {
                                const group = groups.get(id)
                                return group && (<option key={group.id} value={group.id}>{group.title}</option>)
                            })}
                        </select>
                        <button
                            className="button button-primary sw-inline-block"
                            onClick={() => {
                                dispatch({
                                    type: ActionType.ASSIGN_GROUP,
                                    payload: {
                                        groupId: reAssignedGroupId,
                                        termId: term.term_id,
                                    },
                                })
                                setIsReassigning(false)
                            }}
                        >
                            {__('OK', 'swpmu-term-merger')}
                        </button>
                    </div>
                </div>
            )}
        </section>
    )
}

const GroupFooter = (props: Props) => {
    const {dispatch} = useGlobalContext()

    const {group} = props

    return group.terms.length > 1 && (
        <p className="sw-m-0 sw-text-center">
            <button
                className="button button-primary"
                onClick={() => {
                    dispatch({
                        type: ActionType.SET_TARGET_GROUP,
                        payload: group.id,
                    })
                }}
                title={'Merge all terms within this group'}
            >
                {__('Merge ...', 'swpmu-term-merger')}
            </button>
        </p>
    )
}
