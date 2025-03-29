import {Card, CardBody, CardBodyDescription, CardFooter, CardHeader, CardHeaderLabel} from '@/components/ui/Card.tsx'
import ComponentContainer from '@/components/ui/ComponentContainer.tsx'
import TermBox from '@/components/ui/TermBox.tsx'
import {useGlobalContext} from '@/term-merger/context.tsx'
import {ActionType} from '@/term-merger/reducer.ts'
import {Term} from '@/types'
import {useCallback, useEffect, useMemo, useState} from 'react'
import useGetTermsQuery from '@/term-merger/useGetTermsQuery'
import RenderQueryResult from '@/components/RenderQueryResult'
import {adminAjax} from '@/lib/ajax'
import {__, _n, sprintf} from '@wordpress/i18n'

export default function Step3_MergeSelect() {
    const {
        state: {
            currentStep,
            head,
            selected,
            taxonomy,
            terms,
        },
        dispatch,
    } = useGlobalContext()

    const [ready, setReady] = useState<boolean>(false)

    // Get head term object.
    const headTerm = useMemo(
        () => terms.find((term) => head === term.term_id),
        [head, terms],
    )

    // Get terms by selected term_id array
    const selectedTerms = useMemo(
        () => terms.filter((term) => selected.has(term.term_id)),
        [selected, terms],
    )

    // Everything is just for this callback function.
    const mergeTerms = useCallback(() => {
        if (head < 1) {
            throw 'head should be an integer greater than zero.'
        }
        if (selected.size < 2) {
            throw 'selected set should contain one head term id and at least one term id to be merged.'
        }
        if (0 === taxonomy.length) {
            throw 'taxonomy should not be empty.'
        }
        adminAjax.mergeTerms(head, [...selected.values()], taxonomy).then(() => {
            dispatch({
                type: ActionType.RESET,
                payload: 'merge-complete',
            })
        }).catch((reason) => {
            console.error(reason)
        })
    }, [head, selected, taxonomy])

    // You might query terms in this step.
    // You can do it if you are a developer, and you tweak initial value from the PHP script.
    const query = useGetTermsQuery()

    // Make sure that head is in the selected set.
    useEffect(() => {
        if ('merge-select' === currentStep && !selected.has(head)) {
            dispatch({
                type: ActionType.SET_HEAD,
                payload: 0,
            })
        }
    }, [currentStep, selected])

    return (
        <ComponentContainer>
            <Card>
                <CardHeader>
                    <CardHeaderLabel>
                        {__('Step 3/3: Merge Selection', 'swpmu-term-merger')}
                    </CardHeaderLabel>
                </CardHeader>
                <CardBody>
                    <RenderQueryResult query={query}>
                        <CardBodyDescription className="sw-mb-4">
                            {__('Please choose a head term from the term list below. All the other terms will be merged into it.', 'swpmu-term-merger')}
                        </CardBodyDescription>
                        <ul className="sw-m-0 sw-p-0">
                            {selectedTerms.map((term) => (
                                <li key={term.term_id}>
                                    <SingleTerm term={term} />
                                </li>
                            ))}
                        </ul>
                        <CardBodyDescription className="sw-mt-4">
                            <input
                                id="confirm-merge-checkbox"
                                checked={ready}
                                className="sw-mr-1.5"
                                onChange={(e) => setReady(e.target.checked)}
                                type="checkbox"
                            />
                            <label htmlFor="confirm-merge-checkbox">
                                {__('I have backup my database. I am ready to merge terms.', 'swpmu-term-merger')}
                            </label>
                        </CardBodyDescription>
                    </RenderQueryResult>
                </CardBody>
                <CardFooter>
                    <div className="sw-flex sw-justify-between sw-mt-4 sw-mb-2 sw-mx-2">
                        <button
                            className="button button-secondary button-hero"
                            onClick={() => {
                                dispatch({
                                    type: ActionType.SET_CURRENT_STEP,
                                    payload: 'term-select',
                                })
                            }}
                        >
                            &laquo; {__('Previous Step', 'swpmu-term-merger')}
                        </button>
                        <button
                            className="button button-primary button-hero"
                            disabled={!(head > 0 && selected.size > 1 && selected.has(head) && ready)}
                            onClick={() => {
                                const message =
                                    __('[Last Confirmation]', 'swpmu-term-merger') + '\n' +
                                    sprintf(
                                        /* translators: '$d': number of terms, '%s': term name string */
                                        _n(
                                            'Selected %1$d term will be merged into %2$s.',
                                            'Selected %1$d terms will be merged into %2$s.',
                                            selectedTerms.length - 1,
                                            'swpmu-term-merger',
                                        ),
                                        selectedTerms.length - 1,
                                        headTerm!.name,
                                    ) + '\n' +
                                    __('Are you sure?', 'swpmu-term-merger')

                                if (!confirm(message)) {
                                    return false
                                }
                                mergeTerms()
                            }}
                        >
                            {__('Proceed', 'swpmu-term-merger')} &raquo;
                        </button>
                    </div>
                </CardFooter>
            </Card>
        </ComponentContainer>
    )
}

const SingleTerm = ({term}: { term: Term }) => {
    const {
        dispatch,
        state: {head},
    } = useGlobalContext()

    const isHead = head === term.term_id

    return (
        <TermBox
            selected={isHead}
            onClick={() => {
                if (!isHead) {
                    dispatch({
                        type: ActionType.SET_HEAD,
                        payload: term.term_id,
                    })
                }
            }}
        >
            {isHead && <>{__('Head', 'swpmu-term-merger')} &raquo;{' '}</>}
            {term.name}
        </TermBox>
    )
}
