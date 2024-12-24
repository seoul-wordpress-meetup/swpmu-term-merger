export default function ChildComponent({id, sequence}: { id: string; sequence: number }) {
    return (
        <div id={`${id}-${sequence}`}>
            Child component: #{sequence} of '{id}'.
        </div>
    )
}
