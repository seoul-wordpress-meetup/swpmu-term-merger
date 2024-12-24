import {Context, useGlobalContext} from '@/term-merger/context'
import Step4_Complete from '@/term-merger/Step4_Complete.tsx'
import {useGlobalReducer} from '@/term-merger/reducer'
import Step2_TermSelect from '@/term-merger/Step2_TermSelect.tsx'
import Step3_MergeSelect from '@/term-merger/Step3_MergeSelect.tsx'
import {RootProps, State} from '@/types'
import {QueryClient, QueryClientProvider} from '@tanstack/react-query'
import Step1_TaxonomySelect from './Step1_TaxonomySelect.tsx'

const client = new QueryClient()

export type Props = RootProps<State>

export default function TermMerger(props: Props) {
    const [state, dispatch] = useGlobalReducer(props.initialState)

    return (
        <QueryClientProvider client={client}>
            <Context.Provider value={{dispatch, state}}>
                <TermMergerInner />
            </Context.Provider>
        </QueryClientProvider>
    )
}

const TermMergerInner = () => {
    const {
        state: {
            currentStep,
        },
    } = useGlobalContext()

    return (
        <div className="sw-mt-8">
            {'taxonomy-select' === currentStep && <Step1_TaxonomySelect />}
            {'term-select' === currentStep && <Step2_TermSelect />}
            {'merge-select' === currentStep && <Step3_MergeSelect />}
            {'merge-complete' === currentStep && <Step4_Complete />}
        </div>
    )
}
