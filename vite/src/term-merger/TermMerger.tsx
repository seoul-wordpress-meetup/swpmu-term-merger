import {Context, useGlobalContext} from '@/term-merger/context'
import {useGlobalReducer} from '@/term-merger/reducer'
import {type RootProps, type State} from '@/types'
import {QueryClient, QueryClientProvider} from '@tanstack/react-query'
import Step1_TaxonomySelect from './Step1_TaxonomySelect.tsx'
import Step2_TermMerge from './Step2_TermMerge.tsx'

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
            {'term-merge' === currentStep && <Step2_TermMerge />}
        </div>
    )
}
