"use strict";(self.chunk_attribute_heatmap_bundle=self.chunk_attribute_heatmap_bundle||[]).push([["l"],{l8(e,t,a){a.r(t),a.d(t,{AttributeHeatmapExtension:()=>S});var r=a("w8"),l=a("X1"),s=a("kN");let i=e=>(0,l.jsxs)("svg",{viewBox:"0 0 24 24",xmlns:"http://www.w3.org/2000/svg",...e,children:[(0,l.jsx)("rect",{x:"2",y:"2",width:"9",height:"9",rx:"1.5",fill:"currentColor",opacity:"1"}),(0,l.jsx)("rect",{x:"13",y:"2",width:"9",height:"9",rx:"1.5",fill:"currentColor",opacity:"0.6"}),(0,l.jsx)("rect",{x:"2",y:"13",width:"9",height:"9",rx:"1.5",fill:"currentColor",opacity:"0.35"}),(0,l.jsx)("rect",{x:"13",y:"13",width:"9",height:"9",rx:"1.5",fill:"currentColor",opacity:"0.15"})]});var o=a("s96"),n=a("e");let{useAttributeHeatmapGetClassesQuery:d}=n.api.injectEndpoints({endpoints:e=>({attributeHeatmapGetClasses:e.query({query:()=>({url:`${(0,n.getPrefix)()}/bundle/attribute-heatmap/classes`,method:"GET"})})})}),u=e=>{if(""===e.trim())return null;let t="message",a="";for(let r of e.split("\n"))r.startsWith("event: ")?t=r.slice(7):r.startsWith("data: ")&&(a+=r.slice(6));return{event:t,data:a}};var c=a("g3");let p=(0,c.rU)(e=>{let{token:t,css:a}=e;return{controls:a`
    margin-bottom: ${t.marginMD}px;
  `,filter:a`
    min-width: 220px;
  `,chart:a`
    display: flex;
    flex-direction: column;
    gap: ${t.marginSM}px;
  `,row:a`
    display: grid;
    grid-template-columns: minmax(160px, 280px) minmax(240px, 1fr) 64px;
    align-items: center;
    gap: ${t.marginSM}px;
  `,label:a`
    min-width: 0;
  `,title:a`
    overflow: hidden;
    color: ${t.colorText};
    font-weight: 500;
    text-overflow: ellipsis;
    white-space: nowrap;
  `,meta:a`
    overflow: hidden;
    color: ${t.colorTextTertiary};
    font-size: ${t.fontSizeSM}px;
    text-overflow: ellipsis;
    white-space: nowrap;
  `,track:a`
    height: 20px;
    overflow: hidden;
    border-radius: ${t.borderRadiusSM}px;
    background: ${t.colorFillSecondary};
  `,bar:a`
    height: 100%;
    min-width: 0;
    border-radius: ${t.borderRadiusSM}px;
    transition: width ${t.motionDurationMid};
  `,barUsed:a`
    background: ${t.colorSuccess};
  `,barPartiallyUsed:a`
    background: ${t.colorWarning};
  `,barUnused:a`
    background: ${t.colorError};
  `,barNotAnalyzable:a`
    background: ${t.colorTextTertiary};
  `,value:a`
    color: ${t.colorTextSecondary};
    font-variant-numeric: tabular-nums;
    text-align: right;
  `,empty:a`
    margin-top: ${t.marginSM}px;
  `,responsive:a`
    @media (max-width: 720px) {
      grid-template-columns: minmax(120px, 1fr) minmax(120px, 2fr) 52px;
    }
  `}}),m={used:"barUsed",partiallyUsed:"barPartiallyUsed",unused:"barUnused",notAnalyzable:"barNotAnalyzable"},h=e=>Math.round((e.usageRatio??0)*100),g=e=>{let{attributes:t}=e,{styles:a}=p(),{t:i}=(0,r.useTranslation)(),[n,d]=(0,s.useState)("all"),[u,c]=(0,s.useState)("usage-descending"),g=(0,s.useMemo)(()=>[...new Set(t.map(e=>e.group))].sort((e,t)=>e.localeCompare(t)),[t]),b=(0,s.useMemo)(()=>("all"===n?[...t]:t.filter(e=>e.group===n)).sort((e,t)=>{if("name"===u)return e.title.localeCompare(t.title);let a="usage-descending"===u?-1:1;return(h(e)-h(t))*a}),[t,n,u]);return(0,l.jsxs)(l.Fragment,{children:[(0,l.jsxs)(o.Flex,{gap:"small",wrap:!0,className:a.controls,children:[(0,l.jsx)(o.Select,{className:a.filter,"aria-label":i("attribute-heatmap.chart.group-filter"),value:n,options:[{label:i("attribute-heatmap.chart.all-groups"),value:"all"},...g.map(e=>({label:e,value:e}))],onChange:e=>d(e)}),(0,l.jsx)(o.Select,{className:a.filter,"aria-label":i("attribute-heatmap.chart.sort"),value:u,options:[{label:i("attribute-heatmap.chart.sort.usage-descending"),value:"usage-descending"},{label:i("attribute-heatmap.chart.sort.usage-ascending"),value:"usage-ascending"},{label:i("attribute-heatmap.chart.sort.name"),value:"name"}],onChange:e=>c(e)})]}),0===b.length?(0,l.jsx)(o.Alert,{className:a.empty,type:"info",showIcon:!0,message:i("attribute-heatmap.chart.empty")}):(0,l.jsx)("div",{className:a.chart,children:b.map(e=>{let t=h(e),r=null===e.usageRatio?"n/a":`${t}%`,s=i("attribute-heatmap.usage",{used:e.usedCount??"n/a",total:e.totalCount});return(0,l.jsx)(o.Tooltip,{title:s,children:(0,l.jsxs)("div",{className:`${a.row} ${a.responsive}`,children:[(0,l.jsxs)("div",{className:a.label,children:[(0,l.jsx)("div",{className:a.title,children:e.title}),(0,l.jsx)("div",{className:a.meta,children:`${e.group} \xb7 ${e.fieldType}`})]}),(0,l.jsx)("div",{className:a.track,role:"progressbar","aria-label":e.title,"aria-valuemin":0,"aria-valuemax":100,"aria-valuenow":null===e.usageRatio?void 0:t,children:(0,l.jsx)("div",{className:`${a.bar} ${a[m[e.usageState]]}`,style:{width:`${t}%`}})}),(0,l.jsx)("span",{className:a.value,children:r})]})},e.name)})})]})},b=(0,c.rU)(e=>{let{token:t,css:a}=e;return{section:a`
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
  `}}),x={idle:"",collect:"attribute-heatmap.phase.collect",count:"attribute-heatmap.phase.count",objects:"attribute-heatmap.phase.objects",hydrate:"attribute-heatmap.phase.hydrate",done:"attribute-heatmap.phase.done"},y={used:"tileUsed",partiallyUsed:"tilePartiallyUsed",unused:"tileUnused",notAnalyzable:"tileNotAnalyzable"},f={used:"valueUsed",partiallyUsed:"valuePartiallyUsed",unused:"valueUnused",notAnalyzable:"valueNotAnalyzable"},$={used:"dotUsed",partiallyUsed:"dotPartiallyUsed",unused:"dotUnused",notAnalyzable:"dotNotAnalyzable"},v=e=>{var t;let{attribute:a}=e,{styles:s}=b(),{t:i}=(0,r.useTranslation)(),n=[a.name,i("attribute-heatmap.type",{type:a.fieldType}),i("attribute-heatmap.usage",{used:a.usedCount??"n/a",total:a.totalCount})].join("\n");return(0,l.jsx)(o.Tooltip,{title:n,children:(0,l.jsxs)("div",{className:`${s.tile} ${s[y[a.usageState]]}`,children:[(0,l.jsxs)("div",{className:s.tileHeader,children:[(0,l.jsx)("span",{className:s.tileType,children:a.fieldType}),(0,l.jsx)("span",{className:`${s.tileValue} ${s[f[a.usageState]]}`,children:null===(t=a.usageRatio)?"n/a":`${Math.round(100*t)}%`})]}),(0,l.jsx)("div",{className:s.tileTitle,children:a.title})]})})},j=()=>{let{styles:e}=b(),{t}=(0,r.useTranslation)(),[a,i]=(0,s.useState)(null),c=d(),p=(e=>{let{t}=(0,r.useTranslation)(),[a,l]=(0,s.useState)(0),[i,o]=(0,s.useState)("idle"),[d,c]=(0,s.useState)(null),[p,m]=(0,s.useState)(null),[h,g]=(0,s.useState)(!1);return(0,s.useEffect)(()=>{if(null===e)return;let a=new AbortController,r=`${(0,n.getPrefix)()}/bundle/attribute-heatmap/classes/${e}/heatmap/stream`;l(0),o("collect"),c(null),m(null),g(!0);let s=(e,t)=>{"progress"===e?(l(t.percent),o(t.phase)):"result"===e?(c(t),g(!1)):"error"===e&&(m(t.message),g(!1))};return(async()=>{try{let e=await fetch(r,{method:"GET",headers:{Accept:"text/event-stream"},credentials:"same-origin",signal:a.signal});if(!e.ok||null===e.body)throw Error(t("attribute-heatmap.stream.start-error",{status:e.status}));let l=e.body.getReader(),i=new TextDecoder,o=[];for(;;){let{done:e,value:t}=await l.read();if(e){g(!1);break}o.push(i.decode(t,{stream:!0}));let a=o.join("");o.length=0;let r=a.indexOf("\n\n");for(;-1!==r;){let e=a.slice(0,r);a=a.slice(r+2);let t=u(e);t&&s(t.event,JSON.parse(t.data)),r=a.indexOf("\n\n")}""!==a&&o.push(a)}}catch(e){a.signal.aborted||(m(e instanceof Error?e.message:t("attribute-heatmap.stream.unexpected-error")),g(!1))}})(),()=>a.abort()},[e,t]),{progress:a,phase:i,data:d,error:p,isFetching:h}})(a),m=p.progress,h=p.data,y=c.isFetching||p.isFetching;(0,s.useEffect)(()=>{if(null===a&&c.data?.items.length){let e=c.data.items[0];e&&i(e.id)}},[c.data,a]);let f=(0,s.useMemo)(()=>{let e=new Map;if(h)for(let t of h.attributes){let a=t.group,r=e.get(a)??[];r.push(t),e.set(a,r)}return[...e.entries()]},[h]),j=h?.usageSummary,w=[{state:"used",label:t("attribute-heatmap.state.used")},{state:"partiallyUsed",label:t("attribute-heatmap.state.partially-used")},{state:"unused",label:t("attribute-heatmap.state.unused")},{state:"notAnalyzable",label:t("attribute-heatmap.state.not-analyzable")}];return(0,l.jsxs)(o.Content,{padded:!0,padding:{top:"small",x:"medium",bottom:"medium"},children:[(0,l.jsx)(o.Header,{title:t("attribute-heatmap.navigation.title")}),(0,l.jsxs)("div",{className:e.section,children:[c.isError&&(0,l.jsx)(o.Alert,{type:"error",showIcon:!0,message:t("attribute-heatmap.class-list.error")}),c.data&&(0,l.jsx)(o.Select,{className:e.select,placeholder:t("attribute-heatmap.class-select.placeholder"),loading:c.isFetching,options:c.data.items.map(e=>({label:`${e.name} (${e.objectCount} ${t("attribute-heatmap.objects")})`,value:e.id})),value:a,onChange:e=>i(void 0===e?null:String(e))})]}),p.error&&(0,l.jsx)(o.Alert,{type:"error",showIcon:!0,message:p.error}),h&&j&&(0,l.jsxs)(o.Flex,{justify:"space-between",align:"center",className:e.section,children:[(0,l.jsxs)(o.Flex,{gap:"small",children:[(0,l.jsx)(o.Tag,{color:"green",children:t("attribute-heatmap.summary.used",{count:j.used})}),(0,l.jsx)(o.Tag,{color:"orange",children:t("attribute-heatmap.summary.partially-used",{count:j.partiallyUsed})}),(0,l.jsx)(o.Tag,{color:"red",children:t("attribute-heatmap.summary.unused",{count:j.unused})}),(0,l.jsx)(o.Tag,{children:t("attribute-heatmap.summary.not-analyzable",{count:j.notAnalyzable})})]}),y&&(0,l.jsx)(o.Progress,{className:e.progressSummary,percent:m,status:"active",showInfo:!1})]}),y&&!h&&(0,l.jsxs)(o.Flex,{vertical:!0,justify:"center",align:"center",gap:"small",className:e.section,children:[(0,l.jsx)(o.Progress,{className:e.progressLarge,percent:m,status:"active",showInfo:!0}),(0,l.jsx)("span",{className:e.phaseCaption,children:t(x[p.phase])})]}),h&&(0,l.jsx)(o.Tabs,{items:[{key:"heatmap",label:t("attribute-heatmap.tab.heatmap"),children:(0,l.jsxs)(l.Fragment,{children:[(0,l.jsx)("div",{className:e.legend,children:w.map(t=>{let{state:a,label:r}=t;return(0,l.jsxs)(o.Flex,{gap:"small",align:"center",children:[(0,l.jsx)("span",{className:`${e.legendDot} ${e[$[a]]}`}),(0,l.jsx)("span",{children:r})]},a)})}),0===f.length&&(0,l.jsx)(o.Alert,{type:"info",showIcon:!0,message:t("attribute-heatmap.empty")}),f.map(a=>{let[r,s]=a;return(0,l.jsxs)("div",{className:e.group,children:[(0,l.jsx)(o.Header,{title:r,children:(0,l.jsx)("span",{className:e.groupCount,children:t("attribute-heatmap.group-count",{count:s.length})})}),(0,l.jsx)("div",{className:e.tiles,children:s.map(e=>(0,l.jsx)(v,{attribute:e},e.name))})]},r)})]})},{key:"chart",label:t("attribute-heatmap.tab.chart"),children:(0,l.jsx)(g,{attributes:h.attributes})}]})]})},w={onInit:()=>{r.container.get(r.serviceIds.iconLibrary).register({name:"heatmap",component:i}),r.container.get(r.serviceIds.widgetManager).registerWidget({name:"attribute-heatmap",component:j}),r.container.get(r.serviceIds.mainNavRegistry).registerMainNavItem({path:"Attribute Heatmap",label:"attribute-heatmap.navigation.title",icon:"heatmap",order:1e3,perspectivePermission:"dataManagement.attributeHeatmap",widgetConfig:{name:"Attribute Heatmap",id:"attribute-heatmap",component:"attribute-heatmap",config:{translationKey:"attribute-heatmap.navigation.title",icon:{type:"name",value:"heatmap"}}}})}},S={name:"Attribute Heatmap Studio Extension",onStartup(e){let{moduleSystem:t}=e;t.registerModule(w)}}}}]);