using Microsoft.EntityFrameworkCore.Migrations;

#nullable disable

namespace BrilliantEngineering.Infrastructure.Migrations
{
    /// <inheritdoc />
    public partial class AddServiceCategoryDescription : Migration
    {
        /// <inheritdoc />
        protected override void Up(MigrationBuilder migrationBuilder)
        {
            migrationBuilder.AddColumn<string>(
                name: "Description",
                table: "ServiceCategories",
                type: "nvarchar(max)",
                nullable: false,
                defaultValue: "");
        }

        /// <inheritdoc />
        protected override void Down(MigrationBuilder migrationBuilder)
        {
            migrationBuilder.DropColumn(
                name: "Description",
                table: "ServiceCategories");
        }
    }
}
