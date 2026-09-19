"use strict";(self.chunk_attribute_heatmap_bundle=self.chunk_attribute_heatmap_bundle||[]).push([["l"],{Gk(e,t,a){a.r(t),a.d(t,{AttributeHeatmapExtension:()=>f});var r=a("w8"),l=a("X1"),s=a("kN");let o=e=>(0,l.jsxs)("svg",{viewBox:"0 0 24 24",xmlns:"http://www.w3.org/2000/svg",...e,children:[(0,l.jsx)("rect",{x:"2",y:"2",width:"9",height:"9",rx:"1.5",fill:"currentColor",opacity:"1"}),(0,l.jsx)("rect",{x:"13",y:"2",width:"9",height:"9",rx:"1.5",fill:"currentColor",opacity:"0.6"}),(0,l.jsx)("rect",{x:"2",y:"13",width:"9",height:"9",rx:"1.5",fill:"currentColor",opacity:"0.35"}),(0,l.jsx)("rect",{x:"13",y:"13",width:"9",height:"9",rx:"1.5",fill:"currentColor",opacity:"0.15"})]});var i=a("s96"),n=a("e");let{useAttributeHeatmapGetClassesQuery:d}=n.api.injectEndpoints({endpoints:e=>({attributeHeatmapGetClasses:e.query({query:()=>({url:`${(0,n.getPrefix)()}/bundle/attribute-heatmap/classes`,method:"GET"})})})}),c=e=>{if(""===e.trim())return null;let t="message",a="";for(let r of e.split("\n"))r.startsWith("event: ")?t=r.slice(7):r.startsWith("data: ")&&(a+=r.slice(6));return{event:t,data:a}},u=(0,a("g3").rU)(e=>{let{token:t,css:a}=e;return{section:a`
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
    display: flex;
    flex-wrap: wrap;
    gap: ${t.marginXS}px;
    margin-top: ${t.marginXS}px;
  `,tile:a`
    width: 150px;
    min-width: 150px;
    padding: ${t.paddingXS}px ${t.paddingSM}px;
    border-radius: ${t.borderRadiusLG}px;
    border: 1px solid transparent;
    background: ${t.colorBgContainer};
    box-sizing: border-box;
  `,tileTitle:a`
    font-size: ${t.fontSizeSM}px;
    line-height: ${t.lineHeightSM}px;
    color: ${t.colorTextSecondary};
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  `,tileValue:a`
    font-size: ${t.fontSizeLG}px;
    font-weight: ${t.fontWeightStrong};
    margin-top: ${t.marginXXS}px;
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
  `}}),p={idle:"",collect:"Collecting attributes…",count:"Counting objects…",objects:"Analysing object values…",hydrate:"Preparing result…",done:"Done"},g={used:{label:"Used"},partiallyUsed:{label:"Partially used"},unused:{label:"Unused"},notAnalyzable:{label:"Not analyzable"}},m={used:"tileUsed",partiallyUsed:"tilePartiallyUsed",unused:"tileUnused",notAnalyzable:"tileNotAnalyzable"},h={used:"valueUsed",partiallyUsed:"valuePartiallyUsed",unused:"valueUnused",notAnalyzable:"valueNotAnalyzable"},x={used:"dotUsed",partiallyUsed:"dotPartiallyUsed",unused:"dotUnused",notAnalyzable:"dotNotAnalyzable"},b=e=>{var t;let{attribute:a}=e,{styles:r}=u(),s=[a.name,`Type: ${a.fieldType}`,`Used: ${a.usedCount??"n/a"} of ${a.totalCount} objects`].join("\n");return(0,l.jsx)(i.Tooltip,{title:s,children:(0,l.jsxs)("div",{className:`${r.tile} ${r[m[a.usageState]]}`,children:[(0,l.jsx)("div",{className:r.tileTitle,children:a.title}),(0,l.jsx)("div",{className:`${r.tileValue} ${r[h[a.usageState]]}`,children:null===(t=a.usageRatio)?"n/a":`${Math.round(100*t)}%`})]})})},y=()=>{let{styles:e}=u(),[t,a]=(0,s.useState)(null),r=d(),o=(e=>{let[t,a]=(0,s.useState)(0),[r,l]=(0,s.useState)("idle"),[o,i]=(0,s.useState)(null),[d,u]=(0,s.useState)(null),[p,g]=(0,s.useState)(!1);return(0,s.useEffect)(()=>{if(null===e)return;let t=new AbortController,r=`${(0,n.getPrefix)()}/bundle/attribute-heatmap/classes/${e}/heatmap/stream`;a(0),l("collect"),i(null),u(null),g(!0);let s=(e,t)=>{"progress"===e?(a(t.percent),l(t.phase)):"result"===e?(i(t),g(!1)):"error"===e&&(u(t.message),g(!1))};return(async()=>{try{let e=await fetch(r,{method:"GET",headers:{Accept:"text/event-stream"},credentials:"same-origin",signal:t.signal});if(!e.ok||null===e.body)throw Error(`The analysis could not be started (HTTP ${e.status}).`);let a=e.body.getReader(),l=new TextDecoder,o=[];for(;;){let{done:e,value:t}=await a.read();if(e)break;o.push(l.decode(t,{stream:!0}));let r=o.join("");o.length=0;let i=r.indexOf("\n\n");for(;-1!==i;){let e=r.slice(0,i);r=r.slice(i+2);let t=c(e);t&&s(t.event,JSON.parse(t.data)),i=r.indexOf("\n\n")}""!==r&&o.push(r)}}catch(e){t.signal.aborted||(u(e instanceof Error?e.message:"Unexpected error while analyzing."),g(!1))}})(),()=>t.abort()},[e]),{progress:t,phase:r,data:o,error:d,isFetching:p}})(t),m=o.progress,h=o.data,y=r.isFetching||o.isFetching;(0,s.useEffect)(()=>{if(null===t&&r.data?.items.length){let e=r.data.items[0];e&&a(e.id)}},[r.data,t]);let $=(0,s.useMemo)(()=>{let e=new Map;if(h)for(let t of h.attributes){let a=t.group,r=e.get(a)??[];r.push(t),e.set(a,r)}return[...e.entries()]},[h]),f=h?.usageSummary;return(0,l.jsxs)(i.Content,{padded:!0,padding:{top:"small",x:"medium",bottom:"medium"},children:[(0,l.jsx)(i.Header,{title:"Attribute Heatmap"}),(0,l.jsxs)("div",{className:e.section,children:[r.isError&&(0,l.jsx)(i.Alert,{type:"error",showIcon:!0,message:"The list of data object classes could not be loaded."}),r.data&&(0,l.jsx)(i.Select,{className:e.select,placeholder:"Select a data object class",loading:r.isFetching,options:r.data.items.map(e=>({label:`${e.name} (${e.objectCount} objects)`,value:e.id})),value:t,onChange:e=>a(void 0===e?null:String(e))})]}),o.error&&(0,l.jsx)(i.Alert,{type:"error",showIcon:!0,message:o.error}),h&&f&&(0,l.jsxs)(i.Flex,{justify:"space-between",align:"center",className:e.section,children:[(0,l.jsxs)(i.Flex,{gap:"small",children:[(0,l.jsx)(i.Tag,{color:"green",children:`Used: ${f.used}`}),(0,l.jsx)(i.Tag,{color:"orange",children:`Partially used: ${f.partiallyUsed}`}),(0,l.jsx)(i.Tag,{color:"red",children:`Unused: ${f.unused}`}),(0,l.jsx)(i.Tag,{children:`Not analyzable: ${f.notAnalyzable}`})]}),y&&(0,l.jsx)(i.Progress,{className:e.progressSummary,percent:m,status:"active",showInfo:!1})]}),(0,l.jsx)("div",{className:e.legend,children:Object.entries(g).map(t=>{let[a,r]=t;return(0,l.jsxs)(i.Flex,{gap:"small",align:"center",children:[(0,l.jsx)("span",{className:`${e.legendDot} ${e[x[a]]}`}),(0,l.jsx)("span",{children:r.label})]},a)})}),y&&!h&&(0,l.jsxs)(i.Flex,{vertical:!0,justify:"center",align:"center",gap:"small",className:e.section,children:[(0,l.jsx)(i.Progress,{className:e.progressLarge,percent:m,status:"active",showInfo:!0}),(0,l.jsx)("span",{className:e.phaseCaption,children:p[o.phase]})]}),h&&0===$.length&&(0,l.jsx)(i.Alert,{type:"info",showIcon:!0,message:"No attributes found for this class."}),$.map(t=>{let[a,r]=t;return(0,l.jsxs)("div",{className:e.group,children:[(0,l.jsx)(i.Header,{title:a,children:(0,l.jsx)("span",{className:e.groupCount,children:`${r.length} attributes`})}),(0,l.jsx)("div",{className:e.tiles,children:r.map(e=>(0,l.jsx)(b,{attribute:e},e.name))})]},a)})]})},$={onInit:()=>{r.container.get(r.serviceIds.iconLibrary).register({name:"heatmap",component:o}),r.container.get(r.serviceIds.widgetManager).registerWidget({name:"attribute-heatmap",component:y}),r.container.get(r.serviceIds.mainNavRegistry).registerMainNavItem({path:"Attribute Heatmap",label:"attribute-heatmap.navigation.title",icon:"heatmap",order:1e3,perspectivePermission:"dataManagement.attributeHeatmap",widgetConfig:{name:"Attribute Heatmap",id:"attribute-heatmap",component:"attribute-heatmap",config:{translationKey:"attribute-heatmap.navigation.title",icon:{type:"name",value:"heatmap"}}}})}},f={name:"Attribute Heatmap Studio Extension",onStartup(e){let{moduleSystem:t}=e;t.registerModule($)}}}}]);