import ModalDialog from '@/components/ui/ModalDialog.tsx'
import TermBox from '@/components/ui/TermBox.tsx'
import {cn} from '@/lib/utils.ts'
import {useGlobalContext} from '@/term-merger/context.tsx'
import {ActionType} from '@/term-merger/reducer.ts'
import {type Group, TermId} from '@/types'
import {__, sprintf} from '@wordpress/i18n'
import {useEffect, useState} from 'react'
import {adminAjax} from '@/lib/ajax'
import useGetTermsQuery from '@/term-merger/useGetTermsQuery'

export default function MergeDialog() {
    const {
        dispatch,
        state: {
            groups,
            taxonomy,
            targetGroup,
            terms,
        },
    } = useGlobalContext()

    const query = useGetTermsQuery()

    // States
    const [headingTerm, setHeadingTerm] = useState<TermId>(0),
        [isMerging, setIsMerging] = useState<boolean>(false),
        [theGroup, setTheGroup] = useState<Group | undefined>(undefined)

    // Callback
    const proceedMerge = () => {
        if (!theGroup) {
            return
        }

        setIsMerging(true)

        adminAjax.mergeTerms(
            headingTerm,
            theGroup.terms,
            taxonomy,
        ).then(() => {

            // Target terms are vanished.
            // In the group, there is only one term left, the heading term.
            //
            // To do now:
            // - Fetch whole terms list again.
            // - Alert message.
            // - Remove target terms from the group.
            // - Set merging flag to false
            // - Close the dialog.
            query.refetch().then(() => {
                dispatch({
                    type: ActionType.SET_GROUP,
                    payload: {
                        id: theGroup.id,
                        terms: [headingTerm],
                    },
                })
                dispatch({
                    type: ActionType.SET_TARGET_GROUP,
                    payload: 0,
                })
                setIsMerging(false)
            })
            alert(__('Terms have been merged.\nThank you for using SWPMU Term Merger!', 'swpmu-term-merger'))
        }).catch((data) => {
            const errorMessage = data as string
            if (errorMessage) {
                alert(errorMessage)
            }
        })
    }

    // Effects
    useEffect(() => {
        setTheGroup(groups.get(targetGroup))
    }, [targetGroup])

    return !!theGroup && (
        <ModalDialog open={targetGroup > 0}>
            <div className="sw-min-w-52 sw-p-2">
                <div>
                    <h3 className="sw-m-0 sw-mb-6 sw-p-0 sw-text-md">
                        {sprintf(__('Merging Group \'%s\'', 'swpmu-term-merger'), theGroup.title)}
                    </h3>
                    <p>
                        Choose one heading term - all other terms will be merged into this term.
                    </p>
                    <ul
                        className={cn(
                            'sw-m-1 sw-mb-4 sw-p-2 sw-max-h-80 sw-overflow-y-scroll', // generic
                            'sw-border sw-border-solid sw-border-gray-400',         // border
                        )}
                    >
                        {theGroup.terms.map((termId) => {
                            const term = terms.get(termId)
                            return !!term && (
                                <li key={termId}>
                                    <TermBox
                                        className={cn(
                                            'sw-mb-2 sw-p-2',
                                            {
                                                'sw-bg-gray-300': term.term_id === headingTerm,
                                            },
                                        )}
                                    >
                                        <input
                                            checked={headingTerm === term.term_id}
                                            className="sw-me-2"
                                            id={`sw-heading-term-${term.term_id}`}
                                            name="heading-term"
                                            onChange={(e) => {
                                                setHeadingTerm(parseInt(e.target.value))
                                            }}
                                            type="radio"
                                            value={term.term_id}
                                        />
                                        <label
                                            className="hover:sw-cursor-pointer"
                                            htmlFor={`sw-heading-term-${term.term_id}`}>
                                            {term.name}
                                            {term.term_id === headingTerm && (
                                                <strong className="sw-text-xs">
                                                    {' - '}
                                                    {__('Heading Term', 'swpmu-term-merger')}
                                                </strong>
                                            )}
                                        </label>
                                    </TermBox>
                                </li>
                            )
                        })}
                    </ul>
                    <p className="sw-leading-7 sw-bg-gray-300 sw-mt-4 sw-p-2 sw-rounded-2">
                        <span>{__('Press \'Proceed\' button to merge.', 'swpmu-term-merger')}</span>
                        {' '}
                        <strong className="">{__('Please! Backup your database!', 'swpmu-term-merger')}</strong>
                    </p>
                </div>
                {/* Bottom toolbox */}
                <div
                    className={cn(
                        'sw-mt-4 sw-px-4 sw-py-1',   // generic
                        'sw-flex sw-justify-center', // flex
                    )}
                >
                    <button
                        className="button button-secondary sw-me-4"
                        onClick={() => {
                            setHeadingTerm(0)
                            dispatch({
                                type: ActionType.SET_TARGET_GROUP,
                                payload: 0,
                            })
                        }}
                        type="button"
                    >
                        {__('Cancel', 'swpmu-term-merger')}
                    </button>
                    <button
                        className="button button-primary sw-ms-4"
                        disabled={!headingTerm || isMerging}
                        onClick={() => {
                            if (!confirm(__('Are you sure you want to proceed?', 'swpmu-term-merger'))) {
                                return false
                            }
                            proceedMerge()
                        }}
                        type="button"
                    >
                        {isMerging ? __('Merging', 'swpmu-term-merger') : __('Proceed', 'swpmu-term-merger')}
                    </button>
                </div>
            </div>
        </ModalDialog>
    )
}
