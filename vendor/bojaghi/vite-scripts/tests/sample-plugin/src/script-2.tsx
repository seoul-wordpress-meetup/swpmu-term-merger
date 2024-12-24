import {createRoot} from 'react-dom/client'
import ChildComponent from "./ChildComponent.tsx";

declare global {
    let script2: {
        id: string
    }
}

function Component({id}: { id: string }) {
    return (
        <div>
            <ChildComponent id={id} sequence={4}/>
            <ChildComponent id={id} sequence={5}/>
            <ChildComponent id={id} sequence={6}/>
        </div>
    )
}

createRoot(document.getElementById('vite-script-2-root')!)!.render(
    <Component id={script2.id}/>
)
