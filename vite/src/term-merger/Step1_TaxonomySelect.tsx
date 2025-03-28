import {Card, CardBody, CardFooter, CardHeader, CardHeaderLabel} from '@/components/ui/Card.tsx'
import ComponentContainer from '@/components/ui/ComponentContainer.tsx'
import {useGlobalContext} from '@/term-merger/context'
import {ActionType} from '@/term-merger/reducer.ts'
import {__} from '@wordpress/i18n'

export default function Step1_TaxonomySelect() {
    const {
        dispatch,
        state: {
            taxonomies,
            taxonomy,
        },
    } = useGlobalContext()

    console.log('taxonomies', taxonomies)
    console.log('taxonomy', taxonomy)

    return (
        <ComponentContainer className="sw-max-w-md">
            <Card>
                <CardHeader>
                    <CardHeaderLabel>
                        {__('Step 1/2: Choose Taxonomy', 'swpmu-term-merger')}
                    </CardHeaderLabel>
                </CardHeader>
                <CardBody>
                    <p className="sw-m-0">
                        {__('Choose a taxonomy to merge: ', 'swpmu-term-merger')}
                    </p>
                    <p className="sw-mt-2 sw-mb-0 sw-min-w-48">
                        <label
                            className="screen-reader-text"
                            htmlFor="swpmu-tmgr-taxonomy-select"
                        >
                            {__('Taxonomy Selection', 'swpmu-term-merger')}
                        </label>
                        <select
                            id="swpmu-tmgr-taxonomy-select"
                            className="sw-w-content"
                            onChange={(e) => dispatch({
                                type: ActionType.SET_TAXONOMY,
                                payload: e.target.value,
                            })}
                            value={taxonomy}
                        >
                            <option value={''} disabled={true}>
                                {__('Choose Taxonomy', 'swpmu-term-merger')}
                            </option>
                            {Object.entries(taxonomies).map(([key, value]) => (
                                <option key={key} value={key}>{value}</option>
                            ))}
                        </select>
                    </p>
                </CardBody>
                <CardFooter>
                    <div className="sw-flex sw-justify-end sw-mt-4 sw-mb-2 sw-mx-2">
                        <button
                            className="button button-primary button-hero"
                            disabled={taxonomy === ''}
                            onClick={() => {
                                dispatch({
                                    type: ActionType.SET_CURRENT_STEP,
                                    payload: 'term-merge',
                                })
                            }}
                            type="button"
                        >
                            {__('Select Term', 'swpmu-term-merger')} &raquo;
                        </button>
                    </div>
                </CardFooter>
            </Card>
        </ComponentContainer>
    )
}
