#library(heatmap3)

args = commandArgs(TRUE)
input = args[1]
output = args[2]
# print(args)
GO_table = read.table(input,sep = ",",header=TRUE, row.names = 1)
GO_table = as.matrix(GO_table)

png(output,    # create PNG for the heat map        
    width = 2400,        # 5 x 300 pixels
    height = 2400,
    res = 600,            # 300 pixels per inch
    pointsize = 8)        # smaller font size

heatmap(GO_table,margins=c(6,12),cexRow=0.5, cexCol=1.0)
dev.off()