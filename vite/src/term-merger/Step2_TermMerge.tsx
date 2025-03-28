import {Card, CardBody, CardBodyDescription, CardFooter, CardHeader, CardHeaderLabel} from '@/components/ui/Card'
import ComponentContainer from '@/components/ui/ComponentContainer'
import {useGlobalContext} from '@/term-merger/context'
import MergeDialog from '@/term-merger/GroupTermMerger/merge/MergeDialog.tsx'
import {ActionType} from '@/term-merger/reducer'
import {__} from '@wordpress/i18n'
import GroupTermMerger from './GroupTermMerger'

export default function Step2_TermMerge() {
    const {
        dispatch,
    } = useGlobalContext()

    return (
        <ComponentContainer>
            <Card className="sw-max-w-7xl">
                <CardHeader>
                    <CardHeaderLabel>
                        {__('Step 2/2: Term Selection & Merge', 'swpmu-term-merger')}
                    </CardHeaderLabel>
                </CardHeader>
                <CardBody>
                    <CardBodyDescription className="">
                        {__('Assign terms into a group in the right side. Terms of the same group of can be merged.', 'swpmu-term-merger')}
                    </CardBodyDescription>
                    {/* --- The core of term merger ----*/}
                    <GroupTermMerger />
                    {/* --------------------------------*/}
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
                            &laquo; {__('Previous Step', 'swpmu-term-merger')}
                        </button>
                    </div>
                </CardFooter>
            </Card>
            {/* --- The  merge dialog  ----*/}
            <MergeDialog />
            {/* ---------------------------*/}
        </ComponentContainer>
    )
}