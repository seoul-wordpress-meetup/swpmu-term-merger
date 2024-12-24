import {createRoot} from 'react-dom/client'
import ChildComponent from "./ChildComponent.tsx";

declare global {
    let script1: {
        id: string
    }
}

function Component({id}: { id: string }) {
    return (
        <div>
            <ChildComponent id={id} sequence={1}/>
            <ChildComponent id={id} sequence={2}/>
            <ChildComponent id={id} sequence={3}/>
        </div>
    )
}

createRoot(document.getElementById('vite-script-1-root')!)!.render(
    <Component id={script1.id}/>
)
