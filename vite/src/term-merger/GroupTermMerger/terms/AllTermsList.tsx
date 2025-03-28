import TermBox, {type Props as TermBoxProps} from '@/components/ui/TermBox'
import {cn, getGroupByTermId} from '@/lib/utils'
import {useGlobalContext} from '@/term-merger/context'
import {ActionType} from '@/term-merger/reducer'
import useGetTermsQuery from '@/term-merger/useGetTermsQuery'
import type {Term} from '@/types'
import {__} from '@wordpress/i18n'
import {ReactNode} from 'react'

export default function AllTermsList() {
    const {
        dispatch,
        state: {
            selected,
            terms,
            termOrders,
        },
    } = useGlobalContext()

    useGetTermsQuery()

    return (
        <ol
            id="swpmu-terms-list"
            className={cn(
                'sw-list-none sw-m-0 sw-pe-2',                                                // generic
                'sw-border sw-border-y-0 sw-border-s-0 sw-border-solid sw-border-gray-400', // border
                'xl:sw-max-w-4xl lg:sw-max-w-xl md:sw-max-w-lg sw-max-w-[60%]',               // width
                {                                                                             // grid
                    'sw-grid xl:sw-grid-cols-4 md:sw-grid-cols-2': terms.size > 0,
                    'sw-w-3/4': terms.size === 0,
                },
            )}
        >
            {terms.size > 0 && termOrders.map((id) => {
                const term = terms.get(id)
                return term && (
                    <li key={term.term_id}>
                        <InnerTermBox
                            onClick={() => dispatch({
                                type: ActionType.SET_SELECTED,
                                payload: {
                                    termId: term.term_id,
                                    value: true,
                                },
                            })}
                            onCollapse={() => dispatch({
                                type: ActionType.SET_SELECTED,
                                payload: {
                                    termId: term.term_id,
                                    value: false,
                                },
                            })}
                            selected={selected.has(term.term_id)}
                            term={term}
                        />
                    </li>
                )
            })}
            {terms.size === 0 && (
                <li>
                    <p className="sw-text-center">
                        {__('No terms found.', 'swpmu-term-merger')}
                    </p>
                </li>
            )}
        </ol>
    )
}

type InnerTermBoxProps = TermBoxProps & {
    term: Term
    onCollapse?: () => void
}

const InnerTermBox = (props: InnerTermBoxProps) => {
    const {
        dispatch,
        state: {
            assignIndex,
            groups,
            groupOrders,
        },
    } = useGlobalContext()

    const {
        onCollapse,
        selected,
        term,
        ...restProps
    } = props

    const termInfo: [string, ReactNode][] = [
        [
            __('Term ID', 'swpmu-term-merger'),
            <a
                href={`/wp-admin/term.php?taxonomy=post_tag&tag_ID=${term.term_id}`}
                target="term"
            >
                {term.term_id.toString()}
            </a>,
        ],
        [__('Slug', 'swpmu-term-merger'), term.slug.toString()],
        [
            __('Count', 'swpmu-term-merger'),
            term.count > 0 ? (
                <a
                    href={`/wp-admin/edit.php?tag=${term.slug}`}
                    target="related-posts"
                >
                    {term.count.toString()}
                </a>
            ) : (term.count.toString()),
        ],
    ]

    const groupId = assignIndex.get(term.term_id)

    return (
        <TermBox
            className="sw-m-1 sw-p-2"
            selected={selected}
            {...restProps}
        >
            <div
                className={cn('sw-relative')}
            >
                <h4
                    className={cn(
                        'sw-m-0 sw-font-normal',
                    )}
                    onClick={(e) => {
                        if (selected) {
                            onCollapse && onCollapse()
                            e.stopPropagation()
                        }
                    }}
                >
                    {term.name}
                </h4>
                {!!groupId && (
                    <span
                        className={cn(
                            'sw-px-2 sw-py-0',
                            'sw-test-xs sw-bg-blue-300 sw-opacity-70',
                            'sw-absolute sw-bottom-[-8px] sw-right-[-8px]',
                            'sw-rounded-tl-md',
                        )}
                    >
                        #{groupId}
                    </span>
                )}
            </div>
            {selected && (
                <div
                    className={cn(
                        'sw-mt-2 sw-pt-4', // margin, and padding
                        'sw-border sw-border-x-0 sw-border-b-0 sw-border-solid sw-border-gray-500', // border
                        'sw-font-normal',  // font
                    )}
                >
                    <table className="sw-m-0">
                        <tbody>
                        {termInfo.map(([label, value], index) => (
                            <tr key={index}>
                                <th className="sw-font-medium sw-w-20">{label}</th>
                                <td className="">{value}</td>
                            </tr>
                        ))}
                        </tbody>
                    </table>

                    {groups.size > 0 ? (
                        <div className="sw-mt-4">
                            <label
                                className="sw-block sw-mb-2"
                                htmlFor={`group-assign-${term.term_id}`}
                            >
                                {__('Assigned Group', 'swpmu-term-merger')}
                            </label>
                            <select
                                id={`group-assign-${term.term_id}`}
                                onChange={(e) => {
                                    const value = parseInt(e.target.value)
                                    dispatch({
                                        type: ActionType.ASSIGN_GROUP,
                                        payload: {
                                            groupId: value,
                                            termId: term.term_id,
                                        },
                                    })
                                }}
                                value={getGroupByTermId(term.term_id, groups)?.id ?? ''}
                            >
                                <option value={0}>{__('Not assigned', 'swpmu-term-merger')}</option>
                                {groupOrders.map((id) => {
                                    const group = groups.get(id)
                                    return group && (<option key={group.id} value={group.id}>{group.title}</option>)
                                })}
                            </select>
                        </div>
                    ) : (<p>{__('Please create a group to assign this term.', 'swpmu-term-merger')}</p>)}

                    <p className="sw-mt-4 sw-mb-0 sw-text-right">
                        <button
                            className="button button-secondary sw-px-2"
                            onClick={(e) => {
                                onCollapse && onCollapse()
                                e.stopPropagation() // The propagated click event will keep this box open.
                            }}
                            type="button"
                        >
                            {__('Collapse', 'swpmu-term-merger')}
                        </button>
                    </p>
                </div>
            )}
        </TermBox>
    )
}
