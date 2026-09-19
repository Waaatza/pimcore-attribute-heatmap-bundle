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
    gap: ${t.marginXS}px;
    min-width: 0;
    padding: ${t.paddingSM}px ${t.paddingMD}px;
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
  `}}),p={idle:"",collect:"attribute-heatmap.phase.collect",count:"attribute-heatmap.phase.count",objects:"attribute-heatmap.phase.objects",hydrate:"attribute-heatmap.phase.hydrate",done:"attribute-heatmap.phase.done"},m={used:{label:"attribute-heatmap.state.used"},partiallyUsed:{label:"attribute-heatmap.state.partially-used"},unused:{label:"attribute-heatmap.state.unused"},notAnalyzable:{label:"attribute-heatmap.state.not-analyzable"}},g={used:"tileUsed",partiallyUsed:"tilePartiallyUsed",unused:"tileUnused",notAnalyzable:"tileNotAnalyzable"},h={used:"valueUsed",partiallyUsed:"valuePartiallyUsed",unused:"valueUnused",notAnalyzable:"valueNotAnalyzable"},b={used:"dotUsed",partiallyUsed:"dotPartiallyUsed",unused:"dotUnused",notAnalyzable:"dotNotAnalyzable"},x=e=>{var t;let{attribute:a}=e,{styles:s}=u(),{t:i}=(0,r.useTranslation)(),n=[a.name,i("attribute-heatmap.type",{type:a.fieldType}),i("attribute-heatmap.usage",{used:a.usedCount??"n/a",total:a.totalCount})].join("\n");return(0,l.jsx)(o.Tooltip,{title:n,children:(0,l.jsxs)("div",{className:`${s.tile} ${s[g[a.usageState]]}`,children:[(0,l.jsxs)("div",{className:s.tileHeader,children:[(0,l.jsx)("span",{className:s.tileType,children:a.fieldType}),(0,l.jsx)("span",{className:`${s.tileValue} ${s[h[a.usageState]]}`,children:null===(t=a.usageRatio)?"n/a":`${Math.round(100*t)}%`})]}),(0,l.jsx)("div",{className:s.tileTitle,children:a.title})]})})},y=()=>{let{styles:e}=u(),{t}=(0,r.useTranslation)(),[a,i]=(0,s.useState)(null),g=d(),h=(e=>{let{t}=(0,r.useTranslation)(),[a,l]=(0,s.useState)(0),[i,o]=(0,s.useState)("idle"),[d,u]=(0,s.useState)(null),[p,m]=(0,s.useState)(null),[g,h]=(0,s.useState)(!1);return(0,s.useEffect)(()=>{if(null===e)return;let a=new AbortController,r=`${(0,n.getPrefix)()}/bundle/attribute-heatmap/classes/${e}/heatmap/stream`;l(0),o("collect"),u(null),m(null),h(!0);let s=(e,t)=>{"progress"===e?(l(t.percent),o(t.phase)):"result"===e?(u(t),h(!1)):"error"===e&&(m(t.message),h(!1))};return(async()=>{try{let e=await fetch(r,{method:"GET",headers:{Accept:"text/event-stream"},credentials:"same-origin",signal:a.signal});if(!e.ok||null===e.body)throw Error(t("attribute-heatmap.stream.start-error",{status:e.status}));let l=e.body.getReader(),i=new TextDecoder,o=[];for(;;){let{done:e,value:t}=await l.read();if(e){h(!1);break}o.push(i.decode(t,{stream:!0}));let a=o.join("");o.length=0;let r=a.indexOf("\n\n");for(;-1!==r;){let e=a.slice(0,r);a=a.slice(r+2);let t=c(e);t&&s(t.event,JSON.parse(t.data)),r=a.indexOf("\n\n")}""!==a&&o.push(a)}}catch(e){a.signal.aborted||(m(e instanceof Error?e.message:t("attribute-heatmap.stream.unexpected-error")),h(!1))}})(),()=>a.abort()},[e,t]),{progress:a,phase:i,data:d,error:p,isFetching:g}})(a),y=h.progress,f=h.data,$=g.isFetching||h.isFetching;(0,s.useEffect)(()=>{if(null===a&&g.data?.items.length){let e=g.data.items[0];e&&i(e.id)}},[g.data,a]);let j=(0,s.useMemo)(()=>{let e=new Map;if(f)for(let t of f.attributes){let a=t.group,r=e.get(a)??[];r.push(t),e.set(a,r)}return[...e.entries()]},[f]),v=f?.usageSummary;return(0,l.jsxs)(o.Content,{padded:!0,padding:{top:"small",x:"medium",bottom:"medium"},children:[(0,l.jsx)(o.Header,{title:"Attribute Heatmap"}),(0,l.jsxs)("div",{className:e.section,children:[g.isError&&(0,l.jsx)(o.Alert,{type:"error",showIcon:!0,message:t("attribute-heatmap.class-list.error")}),g.data&&(0,l.jsx)(o.Select,{className:e.select,placeholder:t("attribute-heatmap.class-select.placeholder"),loading:g.isFetching,options:g.data.items.map(e=>({label:t("attribute-heatmap.class-select.option",{name:e.name,count:e.objectCount}),value:e.id})),value:a,onChange:e=>i(void 0===e?null:String(e))})]}),h.error&&(0,l.jsx)(o.Alert,{type:"error",showIcon:!0,message:h.error}),f&&v&&(0,l.jsxs)(o.Flex,{justify:"space-between",align:"center",className:e.section,children:[(0,l.jsxs)(o.Flex,{gap:"small",children:[(0,l.jsx)(o.Tag,{color:"green",children:t("attribute-heatmap.summary.used",{count:v.used})}),(0,l.jsx)(o.Tag,{color:"orange",children:t("attribute-heatmap.summary.partially-used",{count:v.partiallyUsed})}),(0,l.jsx)(o.Tag,{color:"red",children:t("attribute-heatmap.summary.unused",{count:v.unused})}),(0,l.jsx)(o.Tag,{children:t("attribute-heatmap.summary.not-analyzable",{count:v.notAnalyzable})})]}),$&&(0,l.jsx)(o.Progress,{className:e.progressSummary,percent:y,status:"active",showInfo:!1})]}),(0,l.jsx)("div",{className:e.legend,children:Object.entries(m).map(a=>{let[r,s]=a;return(0,l.jsxs)(o.Flex,{gap:"small",align:"center",children:[(0,l.jsx)("span",{className:`${e.legendDot} ${e[b[r]]}`}),(0,l.jsx)("span",{children:t(s.label)})]},r)})}),$&&!f&&(0,l.jsxs)(o.Flex,{vertical:!0,justify:"center",align:"center",gap:"small",className:e.section,children:[(0,l.jsx)(o.Progress,{className:e.progressLarge,percent:y,status:"active",showInfo:!0}),(0,l.jsx)("span",{className:e.phaseCaption,children:t(p[h.phase])})]}),f&&0===j.length&&(0,l.jsx)(o.Alert,{type:"info",showIcon:!0,message:t("attribute-heatmap.empty")}),j.map(a=>{let[r,s]=a;return(0,l.jsxs)("div",{className:e.group,children:[(0,l.jsx)(o.Header,{title:r,children:(0,l.jsx)("span",{className:e.groupCount,children:t("attribute-heatmap.group-count",{count:s.length})})}),(0,l.jsx)("div",{className:e.tiles,children:s.map(e=>(0,l.jsx)(x,{attribute:e},e.name))})]},r)})]})},f={onInit:()=>{r.container.get(r.serviceIds.iconLibrary).register({name:"heatmap",component:i}),r.container.get(r.serviceIds.widgetManager).registerWidget({name:"attribute-heatmap",component:y}),r.container.get(r.serviceIds.mainNavRegistry).registerMainNavItem({path:"Attribute Heatmap",label:"attribute-heatmap.navigation.title",icon:"heatmap",order:1e3,perspectivePermission:"dataManagement.attributeHeatmap",widgetConfig:{name:"Attribute Heatmap",id:"attribute-heatmap",component:"attribute-heatmap",config:{translationKey:"attribute-heatmap.navigation.title",icon:{type:"name",value:"heatmap"}}}})}},$={name:"Attribute Heatmap Studio Extension",onStartup(e){let{moduleSystem:t}=e;t.registerModule(f)}}}}]);