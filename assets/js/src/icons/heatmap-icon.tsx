import React from 'react'

export interface HeatmapIconProps extends React.SVGProps<SVGSVGElement> {}

export const HeatmapIcon = (props: HeatmapIconProps): React.JSX.Element => {
  return (
    <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" {...props}>
      <rect x="2" y="2" width="9" height="9" rx="1.5" fill="currentColor" opacity="1" />
      <rect x="13" y="2" width="9" height="9" rx="1.5" fill="currentColor" opacity="0.6" />
      <rect x="2" y="13" width="9" height="9" rx="1.5" fill="currentColor" opacity="0.35" />
      <rect x="13" y="13" width="9" height="9" rx="1.5" fill="currentColor" opacity="0.15" />
    </svg>
  )
}