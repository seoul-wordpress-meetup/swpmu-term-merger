import {Card, CardBody, CardFooter, CardHeader, CardHeaderLabel} from '@/components/ui/Card.tsx'
import ComponentContainer from '@/components/ui/ComponentContainer.tsx'
import {useGlobalContext} from '@/term-merger/context.tsx'
import {ActionType} from '@/term-merger/reducer.ts'
import {__} from '@wordpress/i18n'

export default function Step4_Complete() {
    const {
        dispatch,
    } = useGlobalContext()

    return (
        <ComponentContainer className="sw-max-w-md">
            <Card>
                <CardHeader>
                    <CardHeaderLabel>
                        {__('Complete', 'swpmu-term-merger')}
                    </CardHeaderLabel>
                </CardHeader>
                <CardBody>
                    <div className="sw-text-center">
                        <p className="sw-m-0">
                            <span className="dashicons dashicons-thumbs-up sw-text-6xl sw-w-full sw-h-full" />
                        </p>
                        <p className="sw-mt-3 sw-mb-0 sw-text-xl">
                            {__('Term merge is complete.', 'swpmu-term-merger')}
                        </p>
                    </div>
                </CardBody>
                <CardFooter>
                    <div className="sw-flex sw-justify-center sw-mt-4 sw-mb-2 sw-mx-2">
                        <button
                            className="button button-primary button-hero"
                            onClick={() => {
                                dispatch({
                                    type: ActionType.SET_CURRENT_STEP,
                                    payload: 'taxonomy-select',
                                })
                            }}
                        >
                            {__('Back', 'swpmu-term-merger')}
                        </button>
                    </div>
                </CardFooter>
            </Card>
        </ComponentContainer>
    )
}
