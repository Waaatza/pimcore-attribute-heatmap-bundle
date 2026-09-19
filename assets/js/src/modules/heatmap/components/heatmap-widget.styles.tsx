import { createStyles } from 'antd-style'

export const useStyles = createStyles(({ token, css }) => ({
  section: css`
    margin-bottom: ${token.margin}px;
  `,
  select: css`
    width: 320px;
  `,
  progressSummary: css`
    width: 120px;
  `,
  progressLarge: css`
    width: 360px;
    max-width: 100%;
  `,
  phaseCaption: css`
    font-size: ${token.fontSizeSM}px;
    color: ${token.colorTextTertiary};
  `,
  legend: css`
    display: flex;
    gap: ${token.margin}px;
    margin-bottom: ${token.marginSM}px;
    font-size: ${token.fontSizeSM}px;
    color: ${token.colorTextTertiary};
  `,
  legendDot: css`
    display: inline-block;
    width: 12px;
    height: 12px;
    border-radius: ${token.borderRadiusSM}px;
  `,
  group: css`
    margin-bottom: ${token.margin}px;
  `,
  groupCount: css`
    font-size: ${token.fontSizeSM}px;
    color: ${token.colorTextTertiary};
  `,
  tiles: css`
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
    gap: ${token.marginXS}px;
    margin-top: ${token.marginXS}px;
  `,
  tile: css`
    display: flex;
    flex-direction: column;
    gap: ${token.marginXS}px;
    min-width: 0;
    padding: ${token.paddingSM}px ${token.paddingMD}px;
    border-radius: ${token.borderRadiusLG}px;
    border: 1px solid transparent;
    background: ${token.colorBgContainer};
    box-sizing: border-box;
  `,
  tileHeader: css`
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: ${token.marginXXS}px;
  `,
  tileType: css`
    font-size: ${token.fontSizeSM}px;
    color: ${token.colorTextTertiary};
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  `,
  tileTitle: css`
    font-size: ${token.fontSize}px;
    line-height: ${token.lineHeight}px;
    color: ${token.colorText};
    font-weight: 500;
    word-break: break-word;
    overflow-wrap: anywhere;
  `,
  tileValue: css`
    font-size: ${token.fontSizeLG}px;
    font-weight: ${token.fontWeightStrong};
    flex-shrink: 0;
  `,
  tileUsed: css`
    border-color: ${token.colorSuccess};
    background: ${token.colorSuccessBg};
  `,
  tilePartiallyUsed: css`
    border-color: ${token.colorWarning};
    background: ${token.colorWarningBg};
  `,
  tileUnused: css`
    border-color: ${token.colorError};
    background: ${token.colorErrorBg};
  `,
  tileNotAnalyzable: css`
    border-color: ${token.colorTextTertiary};
    background: ${token.colorFillTertiary};
  `,
  valueUsed: css`
    color: ${token.colorSuccess};
  `,
  valuePartiallyUsed: css`
    color: ${token.colorWarning};
  `,
  valueUnused: css`
    color: ${token.colorError};
  `,
  valueNotAnalyzable: css`
    color: ${token.colorTextTertiary};
  `,
  dotUsed: css`
    background: ${token.colorSuccess};
  `,
  dotPartiallyUsed: css`
    background: ${token.colorWarning};
  `,
  dotUnused: css`
    background: ${token.colorError};
  `,
  dotNotAnalyzable: css`
    background: ${token.colorTextTertiary};
  `
}))