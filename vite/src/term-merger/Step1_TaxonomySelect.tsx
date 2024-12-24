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

    return (
        <ComponentContainer className="sw-max-w-md">
            <Card>
                <CardHeader>
                    <CardHeaderLabel>
                        {__('Step 1/3: Choose Taxonomy', 'swm-term-merger')}
                    </CardHeaderLabel>
                </CardHeader>
                <CardBody>
                    <p className="sw-m-0">
                        {__('Choose a taxonomy to merge: ', 'swm-term-merger')}
                    </p>
                    <p className="sw-mt-2 sw-mb-0 sw-min-w-48">
                        <label
                            className="screen-reader-text"
                            htmlFor="swm-tmgr-taxonomy-select"
                        >
                            {__('Taxonomy Selection', 'swm-term-merger')}
                        </label>
                        <select
                            id="swm-tmgr-taxonomy-select"
                            className="sw-w-content"
                            onChange={(e) => dispatch({
                                type: ActionType.SET_TAXONOMY,
                                payload: e.target.value,
                            })}
                            value={taxonomy}
                        >
                            <option value={''} disabled={true}>
                                {__('Choolse Taxonomy', 'swm-term-merger')}
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
                                    payload: 'term-select',
                                })
                            }}
                            type="button"
                        >
                            {__('Select Term', 'swm-term-merger')} &raquo;
                        </button>
                    </div>
                </CardFooter>
            </Card>
        </ComponentContainer>
    )
}
