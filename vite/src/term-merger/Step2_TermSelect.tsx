import RenderQueryResult from '@/components/RenderQueryResult'
import {Card, CardBody, CardBodyDescription, CardFooter, CardHeader, CardHeaderLabel} from '@/components/ui/Card'
import ComponentContainer from '@/components/ui/ComponentContainer'
import TermBox from '@/components/ui/TermBox.tsx'
import {useGlobalContext} from '@/term-merger/context'
import {ActionType} from '@/term-merger/reducer'
import useGetTermsQuery from '@/term-merger/useGetTermsQuery.ts'
import {Term} from '@/types'
import {__} from '@wordpress/i18n'

export default function Step2_TermSelect() {
    const {
        dispatch,
        state: {
            selected,
        },
    } = useGlobalContext()

    const query = useGetTermsQuery()

    return (
        <ComponentContainer>
            <Card>
                <CardHeader>
                    <CardHeaderLabel>
                        {__('Step 2/3: Term Selection', 'swm-term-merger')}
                    </CardHeaderLabel>
                </CardHeader>
                <CardBody>
                    <CardBodyDescription className="">
                        {__('Please choose two or more terms to merge.', 'swm-term-merger')}
                    </CardBodyDescription>
                    <RenderQueryResult query={query}>
                        <ul className="sw-flex sw-flex-wrap sw-m-0 sw-p-0">
                            {/*****************************************
                             * Each term is listed here.              *
                             ******************************************/}
                            {query.data?.map((term) => (
                                <li
                                    className="sw-w-2/12"
                                    key={term.term_id}
                                >
                                    <SingleTerm term={term} />
                                </li>
                            ))}
                        </ul>
                    </RenderQueryResult>
                </CardBody>
                <CardFooter>
                    <div className="sw-flex sw-justify-between sw-mt-4 sw-mb-2 sw-mx-2">
                        <button
                            className="button button-secondary button-hero"
                            onClick={() => {
                                dispatch({
                                    type: ActionType.SET_CURRENT_STEP,
                                    payload: 'taxonomy-select',
                                })
                            }}
                        >
                            &laquo; {__('Previous Step', 'swm-term-merger')}
                        </button>
                        <button
                            className="button button-primary button-hero"
                            disabled={selected.size < 2}
                            onClick={() => {
                                dispatch({
                                    type: ActionType.SET_CURRENT_STEP,
                                    payload: 'merge-select',
                                })
                            }}
                        >
                            {__('Merge Selection', 'swm-term-merger')} &raquo;
                        </button>
                    </div>
                </CardFooter>
            </Card>
        </ComponentContainer>
    )
}

const SingleTerm = (props: { term: Term }) => {
    const {
        dispatch,
        state: {
            selected,
        },
    } = useGlobalContext()

    const {term} = props

    return (
        <TermBox
            onClick={() => {
                if (selected.has(term.term_id)) {
                    // Removval
                    dispatch({
                        type: ActionType.REMOVE_TERM,
                        payload: term.term_id,
                    })
                } else {
                    // Addition
                    dispatch({
                        type: ActionType.ADD_TERM,
                        payload: term.term_id,
                    })
                }
            }}
            selected={selected.has(term.term_id)}
            title={term.name + (term.description.length ? `\n\n${term.description}` : '')}
        >
            {term.name}
        </TermBox>
    )
}
