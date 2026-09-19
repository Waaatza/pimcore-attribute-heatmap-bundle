"use strict";(self.chunk_attribute_heatmap_bundle=self.chunk_attribute_heatmap_bundle||[]).push([["l"],{Gk(e,t,a){a.r(t),a.d(t,{AttributeHeatmapExtension:()=>$});var r=a("w8"),l=a("X1"),s=a("kN");let i=e=>(0,l.jsxs)("svg",{viewBox:"0 0 24 24",xmlns:"http://www.w3.org/2000/svg",...e,children:[(0,l.jsx)("rect",{x:"2",y:"2",width:"9",height:"9",rx:"1.5",fill:"currentColor",opacity:"1"}),(0,l.jsx)("rect",{x:"13",y:"2",width:"9",height:"9",rx:"1.5",fill:"currentColor",opacity:"0.6"}),(0,l.jsx)("rect",{x:"2",y:"13",width:"9",height:"9",rx:"1.5",fill:"currentColor",opacity:"0.35"}),(0,l.jsx)("rect",{x:"13",y:"13",width:"9",height:"9",rx:"1.5",fill:"currentColor",opacity:"0.15"})]});var o=a("s96"),n=a("e");let{useAttributeHeatmapGetClassesQuery:d}=n.api.injectEndpoints({endpoints:e=>({attributeHeatmapGetClasses:e.query({query:()=>({url:`${(0,n.getPrefix)()}/bundle/attribute-heatmap/classes`,method:"GET"})})})}),c=e=>{if(""===e.trim())return null;let t="message",a="";for(let r of e.split("\n"))r.startsWith("event: ")?t=r.slice(7):r.startsWith("data: ")&&(a+=r.slice(6));return{event:t,data:a}},u=(0,a("g3").rU)(e=>{let{token:t,css:a}=e;return{section:a`
    margin-bottom: ${t.margin}px;
  `,select:a`
    width: 320px;
  `,progressSummary:a`
    width: 120px;
  `,progressLarge:a`
    width: 360px;
    max-width: 100%;
  `,phaseCaption:a`
    font-size: ${t.fontSizeSM}px;
    color: ${t.colorTextTertiary};
  `,legend:a`
    display: flex;
    gap: ${t.margin}px;
    margin-bottom: ${t.marginSM}px;
    font-size: ${t.fontSizeSM}px;
    color: ${t.colorTextTertiary};
  `,legendDot:a`
    display: inline-block;
    width: 12px;
    height: 12px;
    border-radius: ${t.borderRadiusSM}px;
  `,group:a`
    margin-bottom: ${t.margin}px;
  `,groupCount:a`
    font-size: ${t.fontSizeSM}px;
    color: ${t.colorTextTertiary};
  `,tiles:a`
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
    gap: ${t.marginXS}px;
    margin-top: ${t.marginXS}px;
  `,tile:a`
    display: flex;
    flex-direction: column;
    gap: ${t.marginXXS}px;
    min-width: 0;
    padding: ${t.paddingXS}px ${t.paddingSM}px;
    border-radius: ${t.borderRadiusLG}px;
    border: 1px solid transparent;
    background: ${t.colorBgContainer};
    box-sizing: border-box;
  `,tileHeader:a`
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: ${t.marginXXS}px;
  `,tileType:a`
    font-size: ${t.fontSizeSM}px;
    color: ${t.colorTextTertiary};
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  `,tileTitle:a`
    font-size: ${t.fontSize}px;
    line-height: ${t.lineHeight}px;
    color: ${t.colorText};
    font-weight: 500;
    word-break: break-word;
    overflow-wrap: anywhere;
  `,tileValue:a`
    font-size: ${t.fontSizeLG}px;
    font-weight: ${t.fontWeightStrong};
    flex-shrink: 0;
  `,tileUsed:a`
    border-color: ${t.colorSuccess};
    background: ${t.colorSuccessBg};
  `,tilePartiallyUsed:a`
    border-color: ${t.colorWarning};
    background: ${t.colorWarningBg};
  `,tileUnused:a`
    border-color: ${t.colorError};
    background: ${t.colorErrorBg};
  `,tileNotAnalyzable:a`
    border-color: ${t.colorTextTertiary};
    background: ${t.colorFillTertiary};
  `,valueUsed:a`
    color: ${t.colorSuccess};
  `,valuePartiallyUsed:a`
    color: ${t.colorWarning};
  `,valueUnused:a`
    color: ${t.colorError};
  `,valueNotAnalyzable:a`
    color: ${t.colorTextTertiary};
  `,dotUsed:a`
    background: ${t.colorSuccess};
  `,dotPartiallyUsed:a`
    background: ${t.colorWarning};
  `,dotUnused:a`
    background: ${t.colorError};
  `,dotNotAnalyzable:a`
    background: ${t.colorTextTertiary};
  `}}),p={idle:"",collect:"Collecting attributes…",count:"Counting objects…",objects:"Analysing object values…",hydrate:"Preparing result…",done:"Done"},g={used:{label:"Used"},partiallyUsed:{label:"Partially used"},unused:{label:"Unused"},notAnalyzable:{label:"Not analyzable"}},m={used:"tileUsed",partiallyUsed:"tilePartiallyUsed",unused:"tileUnused",notAnalyzable:"tileNotAnalyzable"},h={used:"valueUsed",partiallyUsed:"valuePartiallyUsed",unused:"valueUnused",notAnalyzable:"valueNotAnalyzable"},x={used:"dotUsed",partiallyUsed:"dotPartiallyUsed",unused:"dotUnused",notAnalyzable:"dotNotAnalyzable"},b=e=>{var t;let{attribute:a}=e,{styles:r}=u(),s=[a.name,`Type: ${a.fieldType}`,`Used: ${a.usedCount??"n/a"} of ${a.totalCount} objects`].join("\n");return(0,l.jsx)(o.Tooltip,{title:s,children:(0,l.jsxs)("div",{className:`${r.tile} ${r[m[a.usageState]]}`,children:[(0,l.jsxs)("div",{className:r.tileHeader,children:[(0,l.jsx)("span",{className:r.tileType,children:a.fieldType}),(0,l.jsx)("span",{className:`${r.tileValue} ${r[h[a.usageState]]}`,children:null===(t=a.usageRatio)?"n/a":`${Math.round(100*t)}%`})]}),(0,l.jsx)("div",{className:r.tileTitle,children:a.title})]})})},y=()=>{let{styles:e}=u(),[t,a]=(0,s.useState)(null),r=d(),i=(e=>{let[t,a]=(0,s.useState)(0),[r,l]=(0,s.useState)("idle"),[i,o]=(0,s.useState)(null),[d,u]=(0,s.useState)(null),[p,g]=(0,s.useState)(!1);return(0,s.useEffect)(()=>{if(null===e)return;let t=new AbortController,r=`${(0,n.getPrefix)()}/bundle/attribute-heatmap/classes/${e}/heatmap/stream`;a(0),l("collect"),o(null),u(null),g(!0);let s=(e,t)=>{"progress"===e?(a(t.percent),l(t.phase)):"result"===e?(o(t),g(!1)):"error"===e&&(u(t.message),g(!1))};return(async()=>{try{let e=await fetch(r,{method:"GET",headers:{Accept:"text/event-stream"},credentials:"same-origin",signal:t.signal});if(!e.ok||null===e.body)throw Error(`The analysis could not be started (HTTP ${e.status}).`);let a=e.body.getReader(),l=new TextDecoder,i=[];for(;;){let{done:e,value:t}=await a.read();if(e)break;i.push(l.decode(t,{stream:!0}));let r=i.join("");i.length=0;let o=r.indexOf("\n\n");for(;-1!==o;){let e=r.slice(0,o);r=r.slice(o+2);let t=c(e);t&&s(t.event,JSON.parse(t.data)),o=r.indexOf("\n\n")}""!==r&&i.push(r)}}catch(e){t.signal.aborted||(u(e instanceof Error?e.message:"Unexpected error while analyzing."),g(!1))}})(),()=>t.abort()},[e]),{progress:t,phase:r,data:i,error:d,isFetching:p}})(t),m=i.progress,h=i.data,y=r.isFetching||i.isFetching;(0,s.useEffect)(()=>{if(null===t&&r.data?.items.length){let e=r.data.items[0];e&&a(e.id)}},[r.data,t]);let f=(0,s.useMemo)(()=>{let e=new Map;if(h)for(let t of h.attributes){let a=t.group,r=e.get(a)??[];r.push(t),e.set(a,r)}return[...e.entries()]},[h]),$=h?.usageSummary;return(0,l.jsxs)(o.Content,{padded:!0,padding:{top:"small",x:"medium",bottom:"medium"},children:[(0,l.jsx)(o.Header,{title:"Attribute Heatmap"}),(0,l.jsxs)("div",{className:e.section,children:[r.isError&&(0,l.jsx)(o.Alert,{type:"error",showIcon:!0,message:"The list of data object classes could not be loaded."}),r.data&&(0,l.jsx)(o.Select,{className:e.select,placeholder:"Select a data object class",loading:r.isFetching,options:r.data.items.map(e=>({label:`${e.name} (${e.objectCount} objects)`,value:e.id})),value:t,onChange:e=>a(void 0===e?null:String(e))})]}),i.error&&(0,l.jsx)(o.Alert,{type:"error",showIcon:!0,message:i.error}),h&&$&&(0,l.jsxs)(o.Flex,{justify:"space-between",align:"center",className:e.section,children:[(0,l.jsxs)(o.Flex,{gap:"small",children:[(0,l.jsx)(o.Tag,{color:"green",children:`Used: ${$.used}`}),(0,l.jsx)(o.Tag,{color:"orange",children:`Partially used: ${$.partiallyUsed}`}),(0,l.jsx)(o.Tag,{color:"red",children:`Unused: ${$.unused}`}),(0,l.jsx)(o.Tag,{children:`Not analyzable: ${$.notAnalyzable}`})]}),y&&(0,l.jsx)(o.Progress,{className:e.progressSummary,percent:m,status:"active",showInfo:!1})]}),(0,l.jsx)("div",{className:e.legend,children:Object.entries(g).map(t=>{let[a,r]=t;return(0,l.jsxs)(o.Flex,{gap:"small",align:"center",children:[(0,l.jsx)("span",{className:`${e.legendDot} ${e[x[a]]}`}),(0,l.jsx)("span",{children:r.label})]},a)})}),y&&!h&&(0,l.jsxs)(o.Flex,{vertical:!0,justify:"center",align:"center",gap:"small",className:e.section,children:[(0,l.jsx)(o.Progress,{className:e.progressLarge,percent:m,status:"active",showInfo:!0}),(0,l.jsx)("span",{className:e.phaseCaption,children:p[i.phase]})]}),h&&0===f.length&&(0,l.jsx)(o.Alert,{type:"info",showIcon:!0,message:"No attributes found for this class."}),f.map(t=>{let[a,r]=t;return(0,l.jsxs)("div",{className:e.group,children:[(0,l.jsx)(o.Header,{title:a,children:(0,l.jsx)("span",{className:e.groupCount,children:`${r.length} attributes`})}),(0,l.jsx)("div",{className:e.tiles,children:r.map(e=>(0,l.jsx)(b,{attribute:e},e.name))})]},a)})]})},f={onInit:()=>{r.container.get(r.serviceIds.iconLibrary).register({name:"heatmap",component:i}),r.container.get(r.serviceIds.widgetManager).registerWidget({name:"attribute-heatmap",component:y}),r.container.get(r.serviceIds.mainNavRegistry).registerMainNavItem({path:"Attribute Heatmap",label:"attribute-heatmap.navigation.title",icon:"heatmap",order:1e3,perspectivePermission:"dataManagement.attributeHeatmap",widgetConfig:{name:"Attribute Heatmap",id:"attribute-heatmap",component:"attribute-heatmap",config:{translationKey:"attribute-heatmap.navigation.title",icon:{type:"name",value:"heatmap"}}}})}},$={name:"Attribute Heatmap Studio Extension",onStartup(e){let{moduleSystem:t}=e;t.registerModule(f)}}}}]);